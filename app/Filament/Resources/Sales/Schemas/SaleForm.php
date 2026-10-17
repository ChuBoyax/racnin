<?php

namespace App\Filament\Resources\Sales\Schemas;

use App\Enums\SaleChannel;
use App\Models\ProductVariant;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;

class SaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Order')
                    ->columns(2)
                    ->schema([
                        ToggleButtons::make('channel')
                            ->options(SaleChannel::class)
                            ->default(SaleChannel::Counter)
                            ->inline()
                            ->required(),
                        DateTimePicker::make('sold_at')
                            ->label('Sold at')
                            ->default(now())
                            ->maxDate(now()->endOfDay())
                            ->seconds(false)
                            ->native(false)
                            ->required(),
                        TextInput::make('reference')
                            ->label('Order code')
                            ->placeholder('RN-RCB4U')
                            ->helperText('The code from a website order, if there is one.')
                            ->maxLength(32),
                        TextInput::make('customer_name')
                            ->maxLength(255),
                    ]),

                Section::make('Items')
                    ->schema([
                        Repeater::make('lines')
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make('Item')
                                    ->markAsRequired(),
                                TableColumn::make('Qty')
                                    ->markAsRequired()
                                    ->width('8rem'),
                                TableColumn::make('Amount')
                                    ->width('9rem')
                                    ->alignment('end'),
                            ])
                            ->schema([
                                Select::make('product_variant_id')
                                    ->options(fn (): array => self::menuOptions())
                                    ->searchable()
                                    ->required()
                                    ->live(),
                                TextInput::make('quantity')
                                    ->numeric()
                                    ->integer()
                                    ->minValue(1)
                                    ->maxValue(999)
                                    ->default(1)
                                    ->required()
                                    ->live(onBlur: true),
                                Text::make(fn (Get $get): string => Number::currency(
                                    self::lineTotal($get('product_variant_id'), $get('quantity')),
                                    'PHP',
                                    'en_PH',
                                )),
                            ])
                            ->minItems(1)
                            ->defaultItems(1)
                            ->addActionLabel('Add item'),
                        Text::make(fn (Get $get): string => 'Total: '.Number::currency(
                            collect($get('lines') ?? [])->sum(fn (array $line): float => self::lineTotal($line['product_variant_id'] ?? null, $line['quantity'] ?? null)),
                            'PHP',
                            'en_PH',
                        ))
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold),
                    ]),

                Textarea::make('notes')
                    ->rows(2)
                    ->placeholder('Less ice, paid via GCash…'),
            ]);
    }

    /**
     * What's on the menu, grouped by category like the website.
     *
     * @return array<string, array<int, string>>
     */
    private static function menuOptions(): array
    {
        return self::variants()
            ->groupBy(fn (ProductVariant $variant): string => $variant->product->category->getLabel())
            ->map(fn (Collection $variants): array => $variants
                ->mapWithKeys(fn (ProductVariant $variant): array => [
                    $variant->id => $variant->label().' · '.Number::currency((float) $variant->price, 'PHP', 'en_PH', precision: 0),
                ])
                ->all())
            ->all();
    }

    private static function lineTotal(mixed $variantId, mixed $quantity): float
    {
        $price = (float) (self::variants()->get((int) $variantId)?->price ?? 0);

        return $price * max(0, (int) $quantity);
    }

    /**
     * Variants of products that are on the menu, loaded once per request.
     *
     * @return Collection<int, ProductVariant>
     */
    private static function variants(): Collection
    {
        return once(fn (): Collection => ProductVariant::query()
            ->with('product')
            ->whereHas('product', fn ($query) => $query->where('is_available', true))
            ->get()
            ->sortBy(fn (ProductVariant $variant): string => sprintf('%05d-%05d', $variant->product->sort_order, $variant->sort_order))
            ->keyBy('id'));
    }
}
