<?php

namespace App\Filament\Resources\InventoryItems\Pages;

use App\Filament\Resources\InventoryItems\InventoryItemResource;
use App\Models\InventoryItem;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListInventoryItems extends ListRecords
{
    protected static string $resource = InventoryItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New stock item'),
        ];
    }

    /**
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All items'),
            'low-stock' => Tab::make('Low stock')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->lowStock())
                ->badge(fn (): int => InventoryItem::active()->lowStock()->count())
                ->badgeColor('warning'),
            'out-of-stock' => Tab::make('Out of stock')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('stock_on_hand', '<=', 0))
                ->badge(fn (): int => InventoryItem::active()->where('stock_on_hand', '<=', 0)->count())
                ->badgeColor('danger'),
        ];
    }
}
