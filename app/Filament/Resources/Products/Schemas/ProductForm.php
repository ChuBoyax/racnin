<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Enums\MenuCategory;
use App\Models\InventoryItem;
use App\Models\ProductVariant;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Product')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, ?string $state, string $operation): void {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug((string) $state));
                                }
                            }),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->alphaDash()
                            ->helperText('Matches the item id on the website menu.'),
                        Select::make('category')
                            ->options(MenuCategory::class)
                            ->required()
                            ->native(false),
                        Toggle::make('is_available')
                            ->label('Available to order')
                            ->default(true)
                            ->inline(false),
                        Textarea::make('description')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Sizes, prices & recipes')
                    ->description('Each size or flavor has its own price and recipe. Recording a sale takes the recipe out of stock.')
                    ->schema([
                        Repeater::make('variants')
                            ->hiddenLabel()
                            ->relationship()
                            ->orderColumn('sort_order')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Size or flavor')
                                            ->required()
                                            ->maxLength(64)
                                            ->distinct()
                                            ->datalist(['16 oz', '22 oz', ProductVariant::SINGLE])
                                            ->helperText('Use "'.ProductVariant::SINGLE.'" when there\'s only one option.'),
                                        TextInput::make('price')
                                            ->numeric()
                                            ->required()
                                            ->minValue(0)
                                            ->prefix('₱')
                                            ->live(onBlur: true),
                                    ]),
                                Repeater::make('recipeItems')
                                    ->label('Recipe, per serving')
                                    ->relationship()
                                    ->table([
                                        TableColumn::make('Stock item')
                                            ->markAsRequired(),
                                        TableColumn::make('Amount')
                                            ->markAsRequired(),
                                    ])
                                    ->schema([
                                        Select::make('inventory_item_id')
                                            ->options(fn (): array => self::inventoryOptions())
                                            ->searchable()
                                            ->required()
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                            ->live(),
                                        TextInput::make('quantity')
                                            ->numeric()
                                            ->required()
                                            ->minValue(0.01)
                                            ->suffix(fn (Get $get): ?string => self::inventory()->get($get('inventory_item_id'))?->unit->abbreviation())
                                            ->live(onBlur: true),
                                    ])
                                    ->addActionLabel('Add ingredient')
                                    ->defaultItems(0),
                                Text::make(fn (Get $get): string => self::costSummary($get('recipeItems') ?? [], (float) ($get('price') ?? 0)))
                                    ->color('gray'),
                            ])
                            ->itemLabel(fn (array $state): ?string => self::variantLabel($state))
                            ->collapsible()
                            ->minItems(1)
                            ->defaultItems(1)
                            ->addActionLabel('Add size or flavor'),
                    ]),
            ]);
    }

    /**
     * Stock items keyed by id, loaded once per request.
     *
     * @return Collection<int, InventoryItem>
     */
    private static function inventory(): Collection
    {
        return once(fn (): Collection => InventoryItem::query()->orderBy('name')->get()->keyBy('id'));
    }

    /**
     * @return array<int, string>
     */
    private static function inventoryOptions(): array
    {
        return self::inventory()
            ->map(fn (InventoryItem $item): string => "{$item->name} ({$item->unit->abbreviation()})".($item->is_active ? '' : ' · inactive'))
            ->all();
    }

    /**
     * @param  array<string, array{inventory_item_id?: int|string|null, quantity?: float|string|null}>  $recipe
     */
    private static function costSummary(array $recipe, float $price): string
    {
        $cost = collect($recipe)->sum(fn (array $line): float => (float) ($line['quantity'] ?? 0)
            * (float) (self::inventory()->get($line['inventory_item_id'] ?? null)?->unit_cost ?? 0));

        if ($cost <= 0) {
            return 'Add ingredients to see the cost per serving.';
        }

        $margin = $price > 0 ? (int) round(($price - $cost) / $price * 100) : 0;

        return 'Ingredient cost '.Number::currency($cost, 'PHP', 'en_PH')." per serving · {$margin}% margin";
    }

    /**
     * @param  array<string, mixed>  $state
     */
    private static function variantLabel(array $state): ?string
    {
        if (blank($state['name'] ?? null)) {
            return null;
        }

        return filled($state['price'] ?? null)
            ? "{$state['name']} · ".Number::currency((float) $state['price'], 'PHP', 'en_PH')
            : $state['name'];
    }
}
