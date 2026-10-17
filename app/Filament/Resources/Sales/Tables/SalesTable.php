<?php

namespace App\Filament\Resources\Sales\Tables;

use App\Enums\SaleChannel;
use App\Filament\Resources\Sales\Actions\VoidSaleAction;
use App\Models\Sale;
use App\Models\SaleItem;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class SalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['items', 'user']))
            ->defaultSort('sold_at', 'desc')
            ->columns([
                TextColumn::make('number')
                    ->label('Sale')
                    ->weight('medium')
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->whereKey((int) preg_replace('/\D/', '', $search))),
                TextColumn::make('sold_at')
                    ->label('Sold')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->description(fn (Sale $record): string => $record->sold_at->diffForHumans()),
                TextColumn::make('channel')
                    ->badge(),
                TextColumn::make('items_summary')
                    ->label('Items')
                    ->state(fn (Sale $record): string => $record->items
                        ->map(fn (SaleItem $item): string => "{$item->quantity}× {$item->description()}")
                        ->join(', '))
                    ->limit(60)
                    ->tooltip(fn (Sale $record): string => $record->items
                        ->map(fn (SaleItem $item): string => "{$item->quantity}× {$item->description()}")
                        ->join("\n"))
                    ->wrap(),
                TextColumn::make('customer_name')
                    ->label('Customer')
                    ->placeholder('—')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('reference')
                    ->label('Order code')
                    ->placeholder('—')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total')
                    ->money('PHP', locale: 'en_PH')
                    ->sortable()
                    ->alignEnd()
                    ->weight('medium')
                    ->color(fn (Sale $record): ?string => $record->isVoided() ? 'gray' : null)
                    ->summarize(
                        Sum::make()
                            ->label('Completed sales')
                            ->money('PHP', locale: 'en_PH')
                            ->query(fn (QueryBuilder $query): QueryBuilder => $query->whereNull('voided_at')),
                    ),
                TextColumn::make('status')
                    ->state(fn (Sale $record): string => $record->isVoided() ? 'Voided' : 'Completed')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Voided' ? 'danger' : 'success'),
                TextColumn::make('user.name')
                    ->label('Recorded by')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('channel')
                    ->options(SaleChannel::class),
                TernaryFilter::make('voided_at')
                    ->label('Status')
                    ->nullable()
                    ->placeholder('All sales')
                    ->trueLabel('Voided')
                    ->falseLabel('Completed'),
                Filter::make('sold_at')
                    ->label('Date range')
                    ->schema([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('sold_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('sold_at', '<=', $date))),
            ])
            ->recordActions([
                ViewAction::make(),
                VoidSaleAction::make(),
            ])
            ->emptyStateHeading('No sales yet')
            ->emptyStateDescription('Record walk-in and website orders here. Each sale takes its recipes out of stock.');
    }
}
