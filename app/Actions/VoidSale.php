<?php

namespace App\Actions;

use App\Enums\StockMovementType;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use LogicException;

/**
 * Cancels a sale without erasing it: the sale stays in history marked as
 * voided, and everything it took from stock is put back.
 */
class VoidSale
{
    public function __construct(private RecordStockMovement $recordStockMovement) {}

    public function handle(Sale $sale, ?string $reason = null, ?User $user = null): Sale
    {
        return DB::transaction(function () use ($sale, $reason, $user): Sale {
            $locked = Sale::lockForUpdate()->findOrFail($sale->getKey());

            if ($locked->isVoided()) {
                throw new LogicException("Sale {$locked->number} has already been voided.");
            }

            $movements = $locked->stockMovements()
                ->with('inventoryItem')
                ->where('type', StockMovementType::Sale)
                ->orderBy('inventory_item_id')
                ->get();

            foreach ($movements as $movement) {
                $this->recordStockMovement->handle(
                    $movement->inventoryItem,
                    StockMovementType::SaleVoid,
                    -(float) $movement->quantity,
                    reference: $locked->number,
                    notes: $reason,
                    user: $user,
                    sale: $locked,
                );
            }

            $locked->update(['voided_at' => now(), 'void_reason' => $reason]);

            $sale->setRawAttributes($locked->getAttributes(), sync: true);

            return $locked;
        });
    }
}
