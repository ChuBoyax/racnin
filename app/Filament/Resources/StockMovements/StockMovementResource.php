<?php

namespace App\Filament\Resources\StockMovements;

use App\Enums\StockMovementType;
use App\Filament\Resources\InventoryItems\InventoryItemResource;
use App\Filament\Resources\Sales\SaleResource;
use App\Filament\Resources\StockMovements\Pages\ManageStockMovements;
use App\Models\StockMovement;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/**
 * Every stock change across all items, newest first. Read-only by design:
 * the ledger is written by stock actions and sales, never by hand.
 */
class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static string|UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?string $navigationLabel = 'Stock history';

    protected static ?string $modelLabel = 'stock movement';

    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['inventoryItem', 'sale', 'user']))
            ->defaultSort(fn (Builder $query): Builder => $query->orderByDesc('occurred_at')->orderByDesc('id'))
            ->columns([
                TextColumn::make('occurred_at')
                    ->label('When')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->description(fn (StockMovement $record): string => $record->occurred_at->diffForHumans()),
                TextColumn::make('inventoryItem.name')
                    ->label('Item')
                    ->searchable()
                    ->weight('medium')
                    ->url(fn (StockMovement $record): string => InventoryItemResource::getUrl('edit', ['record' => $record->inventory_item_id])),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('quantity')
                    ->label('Change')
                    ->formatStateUsing(fn (StockMovement $record): string => ((float) $record->quantity > 0 ? '+' : '').$record->inventoryItem->unit->format($record->quantity))
                    ->color(fn (StockMovement $record): string => (float) $record->quantity > 0 ? 'success' : 'danger')
                    ->weight('medium'),
                TextColumn::make('balance_after')
                    ->label('Balance')
                    ->formatStateUsing(fn (StockMovement $record): string => $record->inventoryItem->unit->format($record->balance_after)),
                TextColumn::make('reference')
                    ->placeholder('—')
                    ->searchable()
                    ->url(fn (StockMovement $record): ?string => $record->sale ? SaleResource::getUrl('view', ['record' => $record->sale]) : null),
                TextColumn::make('notes')
                    ->placeholder('—')
                    ->limit(40)
                    ->tooltip(fn (StockMovement $record): ?string => $record->notes)
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label('By')
                    ->placeholder('System')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(StockMovementType::class)
                    ->multiple(),
                SelectFilter::make('inventoryItem')
                    ->label('Item')
                    ->relationship('inventoryItem', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('occurred_at')
                    ->label('Date range')
                    ->schema([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('occurred_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('occurred_at', '<=', $date))),
            ])
            ->emptyStateHeading('No stock movements yet');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageStockMovements::route('/'),
        ];
    }
}
