<?php

namespace App\Filament\Resources\InventoryItems\Tables;

use App\Enums\InventoryCategory;
use App\Filament\Resources\InventoryItems\Actions\CountStockAction;
use App\Filament\Resources\InventoryItems\Actions\RecordWasteAction;
use App\Filament\Resources\InventoryItems\Actions\StockInAction;
use App\Models\InventoryItem;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Number;

class InventoryItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('supplier'))
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(['name', 'sku'])
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (InventoryItem $record): string => $record->sku),
                TextColumn::make('category')
                    ->badge()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('stock_on_hand')
                    ->label('On hand')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn (InventoryItem $record): string => $record->unit->format($record->stock_on_hand))
                    ->color(fn (InventoryItem $record): string => match (true) {
                        $record->isOutOfStock() => 'danger',
                        $record->isLowStock() => 'warning',
                        default => 'success',
                    })
                    ->icon(fn (InventoryItem $record): ?string => $record->isLowStock() ? 'heroicon-m-exclamation-triangle' : null),
                TextColumn::make('reorder_level')
                    ->label('Reorder at')
                    ->sortable()
                    ->formatStateUsing(fn (InventoryItem $record): string => $record->unit->format($record->reorder_level))
                    ->color('gray')
                    ->toggleable(),
                TextColumn::make('unit_cost')
                    ->label('Unit cost')
                    ->sortable()
                    ->formatStateUsing(fn (InventoryItem $record): string => '₱'.Number::format((float) $record->unit_cost, maxPrecision: 4).' / '.$record->unit->abbreviation())
                    ->toggleable(),
                TextColumn::make('stock_value')
                    ->label('Stock value')
                    ->state(fn (InventoryItem $record): float => $record->stockValue())
                    ->money('PHP', locale: 'en_PH')
                    ->alignEnd()
                    ->summarize(
                        Summarizer::make()
                            ->label('Total value')
                            ->money('PHP', locale: 'en_PH')
                            ->using(fn (QueryBuilder $query): float => (float) $query->sum(
                                $query->raw('CASE WHEN stock_on_hand > 0 THEN stock_on_hand * unit_cost ELSE 0 END'),
                            )),
                    ),
                TextColumn::make('supplier.name')
                    ->placeholder('No supplier')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options(InventoryCategory::class)
                    ->multiple(),
                SelectFilter::make('supplier')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('active')
                    ->label('Active items only')
                    ->toggle()
                    ->default()
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),
                TrashedFilter::make(),
            ])
            ->recordActions([
                StockInAction::make()
                    ->iconButton()
                    ->tooltip('Stock in'),
                ActionGroup::make([
                    RecordWasteAction::make(),
                    CountStockAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No stock items yet')
            ->emptyStateDescription('Add the ingredients and supplies you keep, like milk, cups and siomai.');
    }
}
