<?php

namespace App\Filament\Resources\InventoryItems\Pages;

use App\Actions\RecordStockMovement;
use App\Enums\StockMovementType;
use App\Filament\Resources\InventoryItems\InventoryItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInventoryItem extends CreateRecord
{
    protected static string $resource = InventoryItemResource::class;

    private float $openingStock = 0;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->openingStock = (float) ($data['opening_stock'] ?? 0);

        unset($data['opening_stock']);

        return $data;
    }

    /**
     * Opening stock goes through the ledger like any other change, so the
     * item's history starts with where it began.
     */
    protected function afterCreate(): void
    {
        if ($this->openingStock > 0) {
            app(RecordStockMovement::class)->handle(
                $this->record,
                StockMovementType::Adjustment,
                $this->openingStock,
                notes: 'Opening stock',
                user: auth()->user(),
            );
        }
    }
}
