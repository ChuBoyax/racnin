<?php

namespace App\Filament\Resources\InventoryItems\Schemas;

use App\Enums\InventoryCategory;
use App\Enums\StockUnit;
use App\Models\InventoryItem;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class InventoryItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Item')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Fresh milk'),
                        TextInput::make('sku')
                            ->label('SKU')
                            ->required()
                            ->maxLength(32)
                            ->unique(ignoreRecord: true)
                            ->placeholder('MILK-FRESH')
                            ->helperText('A short code you can search by.')
                            ->dehydrateStateUsing(fn (?string $state): ?string => $state ? strtoupper($state) : null),
                        Select::make('category')
                            ->options(InventoryCategory::class)
                            ->required()
                            ->native(false),
                        Select::make('unit')
                            ->label('Counted in')
                            ->options(StockUnit::class)
                            ->required()
                            ->native(false)
                            ->live()
                            ->disabled(fn (?InventoryItem $record): bool => (bool) $record?->stockMovements()->exists())
                            ->helperText(fn (?InventoryItem $record): string => $record?->stockMovements()->exists()
                                ? 'Locked once stock has been recorded, so the history stays accurate.'
                                : 'Use the smallest unit you measure in. Recipes use the same unit.'),
                    ]),

                Section::make('Stock & cost')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('stock_on_hand')
                            ->label('On hand')
                            ->state(fn (InventoryItem $record): string => $record->unit->format($record->stock_on_hand))
                            ->badge()
                            ->color(fn (InventoryItem $record): string => match (true) {
                                $record->isOutOfStock() => 'danger',
                                $record->isLowStock() => 'warning',
                                default => 'success',
                            })
                            ->helperText('Changes only through stock in, waste, counts and sales.')
                            ->visibleOn('edit'),
                        TextInput::make('opening_stock')
                            ->label('Opening stock')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->suffix(fn (Get $get): ?string => self::unitSuffix($get('unit')))
                            ->helperText('What you have on the shelf right now.')
                            ->visibleOn('create'),
                        TextInput::make('reorder_level')
                            ->label('Reorder at')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->default(0)
                            ->suffix(fn (Get $get): ?string => self::unitSuffix($get('unit')))
                            ->helperText('Flagged as low stock at or below this.'),
                        TextInput::make('unit_cost')
                            ->label('Cost per unit')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->step(0.0001)
                            ->default(0)
                            ->prefix('₱')
                            ->suffix(fn (Get $get): ?string => ($suffix = self::unitSuffix($get('unit'))) ? "per {$suffix}" : null)
                            ->helperText('Updated automatically when you stock in with a price.'),
                    ]),

                Section::make('Supplier & notes')
                    ->columns(2)
                    ->schema([
                        Select::make('supplier_id')
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(32),
                            ]),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Inactive items are hidden from recipe pickers.'),
                        Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    private static function unitSuffix(StockUnit|string|null $unit): ?string
    {
        $unit = is_string($unit) ? StockUnit::tryFrom($unit) : $unit;

        return $unit?->abbreviation();
    }
}
