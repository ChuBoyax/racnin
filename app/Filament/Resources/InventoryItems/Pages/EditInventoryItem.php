<?php

namespace App\Filament\Resources\InventoryItems\Pages;

use App\Filament\Resources\InventoryItems\Actions\CountStockAction;
use App\Filament\Resources\InventoryItems\Actions\RecordWasteAction;
use App\Filament\Resources\InventoryItems\Actions\StockInAction;
use App\Filament\Resources\InventoryItems\InventoryItemResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditInventoryItem extends EditRecord
{
    protected static string $resource = InventoryItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            StockInAction::make()
                ->after(fn () => $this->dispatch('refreshStockHistory')),
            RecordWasteAction::make()
                ->outlined()
                ->after(fn () => $this->dispatch('refreshStockHistory')),
            CountStockAction::make()
                ->outlined()
                ->after(fn () => $this->dispatch('refreshStockHistory')),
            ActionGroup::make([
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ]),
        ];
    }
}
