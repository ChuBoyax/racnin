<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\InventoryItems\Actions\StockInAction;
use App\Filament\Resources\InventoryItems\InventoryItemResource;
use App\Models\InventoryItem;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LowStockItems extends TableWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Running low';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => InventoryItem::query()->active()->lowStock()->with('supplier'))
            // Furthest under its reorder level first.
            ->defaultSort(fn (Builder $query): Builder => $query->orderByRaw('stock_on_hand - reorder_level'))
            ->columns([
                TextColumn::make('name')
                    ->weight('medium')
                    ->description(fn (InventoryItem $record): string => $record->sku),
                TextColumn::make('category')
                    ->badge(),
                TextColumn::make('stock_on_hand')
                    ->label('On hand')
                    ->badge()
                    ->formatStateUsing(fn (InventoryItem $record): string => $record->unit->format($record->stock_on_hand))
                    ->color(fn (InventoryItem $record): string => $record->isOutOfStock() ? 'danger' : 'warning'),
                TextColumn::make('reorder_level')
                    ->label('Reorder at')
                    ->formatStateUsing(fn (InventoryItem $record): string => $record->unit->format($record->reorder_level))
                    ->color('gray'),
                TextColumn::make('supplier.name')
                    ->placeholder('No supplier')
                    ->description(fn (InventoryItem $record): ?string => $record->supplier?->phone),
            ])
            ->recordActions([
                StockInAction::make(),
            ])
            ->recordUrl(fn (InventoryItem $record): string => InventoryItemResource::getUrl('edit', ['record' => $record]))
            ->paginated([5, 10, 25])
            ->emptyStateHeading('Everything is well stocked')
            ->emptyStateDescription('Items show up here when they reach their reorder level.')
            ->emptyStateIcon(Heroicon::OutlinedCheckCircle);
    }
}
