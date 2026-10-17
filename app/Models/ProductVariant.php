<?php

namespace App\Models;

use Database\Factories\ProductVariantFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['product_id', 'name', 'price', 'sort_order'])]
class ProductVariant extends Model
{
    /** @use HasFactory<ProductVariantFactory> */
    use HasFactory;

    /**
     * Variants named like this are the product's only option and are left
     * out of labels ("Nachos", not "Nachos (Regular)").
     */
    public const SINGLE = 'Regular';

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    /**
     * @return HasMany<RecipeItem, $this>
     */
    public function recipeItems(): HasMany
    {
        return $this->hasMany(RecipeItem::class);
    }

    /**
     * @return HasMany<SaleItem, $this>
     */
    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * "Cafe Mocha (22 oz)", or just "Nachos" for single-option products.
     */
    public function label(): string
    {
        return $this->name === self::SINGLE ? $this->product->name : "{$this->product->name} ({$this->name})";
    }

    /**
     * Ingredient and packaging cost of one serving, at current unit costs.
     */
    public function costPerServing(): float
    {
        return round($this->recipeItems->sum(
            fn (RecipeItem $recipeItem): float => (float) $recipeItem->quantity * (float) $recipeItem->inventoryItem->unit_cost,
        ), 2);
    }

    /**
     * How many servings the current stock can make, limited by the scarcest
     * ingredient. Null when the variant has no recipe yet.
     */
    public function servingsAvailable(): ?int
    {
        if ($this->recipeItems->isEmpty()) {
            return null;
        }

        return (int) max(0, $this->recipeItems->min(
            fn (RecipeItem $recipeItem): float => floor((float) $recipeItem->inventoryItem->stock_on_hand / max((float) $recipeItem->quantity, 0.01)),
        ));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }
}
