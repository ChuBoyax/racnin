<?php

namespace App\Filament\Resources\InventoryItems\RelationManagers;

use App\Enums\StockMovementType;
use App\Filament\Resources\Sales\SaleResource;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Livewire\Attributes\On;

/**
 * The item's ledger. Read-only: movements are only ever added by stock
 * actions and sales, never edited.
 */
class StockMovementsRelationManager extends RelationManager
{
    protected static string $relationship = 'stockMovements';

    protected static ?string $title = 'Stock history';

    public function isReadOnly(): bool
    {
        return true;
    }

    #[On('refreshStockHistory')]
    public function refreshStockHistory(): void
    {
        // Re-rendering is enough: the table re-queries the ledger.
    }

    public function table(Table $table): Table
    {
        /** @var InventoryItem $item */
        $item = $this->getOwnerRecord();

        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['user', 'sale']))
            ->defaultSort(fn ($query) => $query->orderByDesc('occurred_at')->orderByDesc('id'))
            ->columns([
                TextColumn::make('occurred_at')
                    ->label('When')
                    ->dateTime('M j, Y g:i A')
                    ->description(fn (StockMovement $record): string => $record->occurred_at->diffForHumans()),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('quantity')
                    ->label('Change')
                    ->formatStateUsing(fn (string $state): string => ((float) $state > 0 ? '+' : '').$item->unit->format($state))
                    ->color(fn (string $state): string => (float) $state > 0 ? 'success' : 'danger')
                    ->weight('medium'),
                TextColumn::make('balance_after')
                    ->label('Balance')
                    ->formatStateUsing(fn (string $state): string => $item->unit->format($state)),
                TextColumn::make('reference')
                    ->placeholder('—')
                    ->url(fn (StockMovement $record): ?string => $record->sale ? SaleResource::getUrl('view', ['record' => $record->sale]) : null),
                TextColumn::make('notes')
                    ->placeholder('—')
                    ->limit(40)
                    ->tooltip(fn (StockMovement $record): ?string => $record->notes),
                TextColumn::make('user.name')
                    ->label('By')
                    ->placeholder('System'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(StockMovementType::class)
                    ->multiple(),
            ])
            ->emptyStateHeading('No stock recorded yet')
            ->emptyStateDescription('Stock in, waste, counts and sales will show up here.');
    }
}
