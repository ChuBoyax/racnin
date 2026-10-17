<?php

namespace App\Filament\Resources\Products\Tables;

use App\Enums\MenuCategory;
use App\Models\Product;
use App\Models\ProductVariant;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('variants.recipeItems.inventoryItem'))
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (Product $record): ?string => str($record->description)->limit(50)->toString() ?: null),
                TextColumn::make('category')
                    ->badge()
                    ->sortable(),
                TextColumn::make('prices')
                    ->state(fn (Product $record): string => $record->variants
                        ->map(fn (ProductVariant $variant): string => ($variant->name === ProductVariant::SINGLE ? '' : "{$variant->name} ")
                            .Number::currency((float) $variant->price, 'PHP', 'en_PH', precision: 0))
                        ->join(' · ')),
                TextColumn::make('servings')
                    ->label('Can make')
                    ->state(fn (Product $record): ?int => $record->variants->first()?->servingsAvailable())
                    ->formatStateUsing(fn (?int $state): string => match (true) {
                        $state === null => 'No recipe',
                        $state === 0 => 'Out of stock',
                        default => Number::format($state),
                    })
                    ->placeholder('No recipe')
                    ->badge()
                    ->color(fn (?int $state): string => match (true) {
                        $state === null => 'gray',
                        $state === 0 => 'danger',
                        $state < 10 => 'warning',
                        default => 'success',
                    })
                    ->tooltip('Servings of the first size that current stock can make.'),
                TextColumn::make('food_cost')
                    ->label('Cost · margin')
                    ->state(function (Product $record): ?string {
                        $variant = $record->variants->first();

                        if (! $variant || $variant->recipeItems->isEmpty()) {
                            return null;
                        }

                        $cost = $variant->costPerServing();
                        $margin = (float) $variant->price > 0 ? (int) round(((float) $variant->price - $cost) / (float) $variant->price * 100) : 0;

                        return Number::currency($cost, 'PHP', 'en_PH')." · {$margin}%";
                    })
                    ->placeholder('—')
                    ->color('gray')
                    ->toggleable(),
                ToggleColumn::make('is_available')
                    ->label('Available'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options(MenuCategory::class)
                    ->multiple(),
                TernaryFilter::make('is_available')
                    ->label('Availability')
                    ->trueLabel('Available')
                    ->falseLabel('Unavailable'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No products yet')
            ->emptyStateDescription('Run the seeder to import the website menu, or add a product.');
    }
}
