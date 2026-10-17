<?php

namespace App\Actions;

use App\Enums\StockMovementType;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use App\Models\User;

/**
 * Records a physical count: the difference between what was counted and
 * what the system expected becomes an adjustment.
 */
class RecordStockCount
{
    public function __construct(private RecordStockMovement $recordStockMovement) {}

    /**
     * Returns null when the count already matches the system.
     */
    public function handle(InventoryItem $item, float $countedQuantity, ?string $notes = null, ?User $user = null): ?StockMovement
    {
        $difference = round($countedQuantity - (float) $item->fresh()->stock_on_hand, 2);

        if ($difference == 0.0) {
            return null;
        }

        return $this->recordStockMovement->handle(
            $item,
            StockMovementType::Adjustment,
            $difference,
            notes: $notes ?? 'Physical count',
            user: $user,
        );
    }
}
