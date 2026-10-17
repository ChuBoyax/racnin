<?php

namespace App\Actions;

use App\Enums\SaleChannel;
use App\Enums\StockMovementType;
use App\Models\InventoryItem;
use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Saves a sale at current menu prices and takes every ingredient its
 * recipes use out of stock, as one transaction.
 */
class RecordSale
{
    public function __construct(private RecordStockMovement $recordStockMovement) {}

    /**
     * @param  iterable<array{product_variant_id: int|string, quantity: int|string}>  $lines
     */
    public function handle(
        iterable $lines,
        SaleChannel $channel = SaleChannel::Counter,
        ?string $reference = null,
        ?string $customerName = null,
        ?string $notes = null,
        ?User $user = null,
        ?CarbonInterface $soldAt = null,
    ): Sale {
        // The same variant entered twice becomes one line.
        $quantities = collect($lines)
            ->filter(fn (array $line): bool => (int) $line['quantity'] > 0)
            ->groupBy(fn (array $line): int => (int) $line['product_variant_id'])
            ->map(fn ($group): int => $group->sum(fn (array $line): int => (int) $line['quantity']));

        if ($quantities->isEmpty()) {
            throw new InvalidArgumentException('A sale needs at least one item.');
        }

        return DB::transaction(function () use ($quantities, $channel, $reference, $customerName, $notes, $user, $soldAt): Sale {
            $variants = ProductVariant::query()
                ->with(['product', 'recipeItems'])
                ->findMany($quantities->keys())
                ->keyBy('id');

            $sale = Sale::create([
                'user_id' => $user?->getKey(),
                'channel' => $channel,
                'reference' => $reference,
                'customer_name' => $customerName,
                'notes' => $notes,
                'sold_at' => $soldAt ?? now(),
            ]);

            $usage = [];

            foreach ($quantities as $variantId => $quantity) {
                $variant = $variants->get($variantId) ?? throw new InvalidArgumentException("Menu item #{$variantId} no longer exists.");

                $sale->items()->create([
                    'product_variant_id' => $variant->getKey(),
                    'product_name' => $variant->product->name,
                    'variant_name' => $variant->name,
                    'quantity' => $quantity,
                    'unit_price' => $variant->price,
                    'line_total' => round((float) $variant->price * $quantity, 2),
                ]);

                foreach ($variant->recipeItems as $recipeItem) {
                    $usage[$recipeItem->inventory_item_id] = ($usage[$recipeItem->inventory_item_id] ?? 0) + (float) $recipeItem->quantity * $quantity;
                }
            }

            $sale->update(['total' => $sale->items()->sum('line_total')]);

            // Always lock items in the same order so concurrent sales can't deadlock.
            ksort($usage);
            $items = InventoryItem::withTrashed()->findMany(array_keys($usage))->keyBy('id');

            foreach ($usage as $itemId => $quantity) {
                $this->recordStockMovement->handle(
                    $items[$itemId],
                    StockMovementType::Sale,
                    -$quantity,
                    reference: $sale->number,
                    user: $user,
                    sale: $sale,
                    occurredAt: $sale->sold_at,
                );
            }

            return $sale;
        });
    }
}
