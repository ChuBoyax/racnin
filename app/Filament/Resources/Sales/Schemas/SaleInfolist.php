<?php

namespace App\Filament\Resources\Sales\Schemas;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

class SaleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Voided')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->iconColor('danger')
                    ->description('This sale was cancelled and its ingredients went back into stock.')
                    ->columns(2)
                    ->visible(fn (Sale $record): bool => $record->isVoided())
                    ->schema([
                        TextEntry::make('voided_at')
                            ->dateTime('M j, Y g:i A'),
                        TextEntry::make('void_reason')
                            ->label('Reason')
                            ->placeholder('No reason given'),
                    ]),

                Section::make()
                    ->columns(4)
                    ->schema([
                        TextEntry::make('sold_at')
                            ->label('Sold')
                            ->dateTime('M j, Y g:i A'),
                        TextEntry::make('channel')
                            ->badge(),
                        TextEntry::make('customer_name')
                            ->label('Customer')
                            ->placeholder('—'),
                        TextEntry::make('total')
                            ->money('PHP', locale: 'en_PH')
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold),
                        TextEntry::make('reference')
                            ->label('Order code')
                            ->placeholder('—'),
                        TextEntry::make('user.name')
                            ->label('Recorded by')
                            ->placeholder('—'),
                        TextEntry::make('notes')
                            ->placeholder('—')
                            ->columnSpan(2),
                    ]),

                Section::make('Items')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make('Item'),
                                TableColumn::make('Qty')
                                    ->width('6rem'),
                                TableColumn::make('Price')
                                    ->width('8rem')
                                    ->alignment('end'),
                                TableColumn::make('Amount')
                                    ->width('8rem')
                                    ->alignment('end'),
                            ])
                            ->schema([
                                TextEntry::make('product_name')
                                    ->formatStateUsing(fn (SaleItem $record): string => $record->description()),
                                TextEntry::make('quantity'),
                                TextEntry::make('unit_price')
                                    ->money('PHP', locale: 'en_PH')
                                    ->alignEnd(),
                                TextEntry::make('line_total')
                                    ->money('PHP', locale: 'en_PH')
                                    ->weight(FontWeight::Medium)
                                    ->alignEnd(),
                            ]),
                    ]),

                Section::make('Stock used')
                    ->description('What this sale\'s recipes took out of stock (and put back, if voided).')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        RepeatableEntry::make('stockMovements')
                            ->hiddenLabel()
                            ->placeholder('These items had no recipe, so no stock was used.')
                            ->table([
                                TableColumn::make('Stock item'),
                                TableColumn::make('Type'),
                                TableColumn::make('Change')
                                    ->alignment('end'),
                            ])
                            ->schema([
                                TextEntry::make('inventoryItem.name'),
                                TextEntry::make('type')
                                    ->badge(),
                                TextEntry::make('quantity')
                                    ->formatStateUsing(fn (StockMovement $record): string => ((float) $record->quantity > 0 ? '+' : '').$record->inventoryItem->unit->format($record->quantity))
                                    ->color(fn (StockMovement $record): string => (float) $record->quantity > 0 ? 'success' : 'danger')
                                    ->alignEnd(),
                            ]),
                    ]),
            ]);
    }
}
