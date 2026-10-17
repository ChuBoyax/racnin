<?php

namespace App\Actions;

use App\Enums\StockMovementType;
use App\Models\InventoryItem;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * The only way stock changes: applies a signed quantity to an item, keeps
 * the running balance in the ledger, and re-averages cost on purchases.
 */
class RecordStockMovement
{
    public function handle(
        InventoryItem $item,
        StockMovementType $type,
        float $quantity,
        ?float $unitCost = null,
        ?string $reference = null,
        ?string $notes = null,
        ?User $user = null,
        ?Sale $sale = null,
        ?CarbonInterface $occurredAt = null,
    ): StockMovement {
        $quantity = round($quantity, 2);

        if ($quantity == 0.0) {
            throw new InvalidArgumentException('A stock movement needs a quantity other than zero.');
        }

        return DB::transaction(function () use ($item, $type, $quantity, $unitCost, $reference, $notes, $user, $sale, $occurredAt): StockMovement {
            // Lock the row so two sales at the same moment can't both read the old balance.
            $locked = InventoryItem::withTrashed()->lockForUpdate()->findOrFail($item->getKey());

            $onHand = (float) $locked->stock_on_hand;
            $balance = round($onHand + $quantity, 2);

            if ($type === StockMovementType::Purchase && $unitCost !== null) {
                $locked->unit_cost = $this->movingAverageCost($onHand, (float) $locked->unit_cost, $quantity, $unitCost);
            }

            $locked->stock_on_hand = $balance;
            $locked->save();

            $item->setRawAttributes($locked->getAttributes(), sync: true);

            return $locked->stockMovements()->create([
                'sale_id' => $sale?->getKey(),
                'user_id' => $user?->getKey(),
                'type' => $type,
                'quantity' => $quantity,
                'balance_after' => $balance,
                'unit_cost' => $unitCost ?? $locked->unit_cost,
                'reference' => $reference,
                'notes' => $notes,
                'occurred_at' => $occurredAt ?? now(),
            ]);
        });
    }

    /**
     * Blend the cost of new stock with what's already on the shelf, so the
     * item's value reflects what was actually paid over time.
     */
    private function movingAverageCost(float $onHand, float $currentCost, float $received, float $receivedCost): float
    {
        if ($onHand <= 0) {
            return $receivedCost;
        }

        return round((($onHand * $currentCost) + ($received * $receivedCost)) / ($onHand + $received), 4);
    }
}
