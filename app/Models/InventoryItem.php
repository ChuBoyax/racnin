<?php

namespace App\Models;

use App\Enums\InventoryCategory;
use App\Enums\StockUnit;
use Database\Factories\InventoryItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * An ingredient or supply the shop keeps in stock. The on-hand quantity is
 * never edited directly; it only changes through stock movements.
 */
#[Fillable(['supplier_id', 'name', 'sku', 'category', 'unit', 'reorder_level', 'unit_cost', 'is_active', 'notes'])]
class InventoryItem extends Model
{
    /** @use HasFactory<InventoryItemFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @return BelongsTo<Supplier, $this>
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * @return HasMany<StockMovement, $this>
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * @return HasMany<RecipeItem, $this>
     */
    public function recipeItems(): HasMany
    {
        return $this->hasMany(RecipeItem::class);
    }

    public function isOutOfStock(): bool
    {
        return (float) $this->stock_on_hand <= 0;
    }

    public function isLowStock(): bool
    {
        return (float) $this->stock_on_hand <= (float) $this->reorder_level;
    }

    /**
     * What the stock on hand is worth at its current unit cost.
     */
    public function stockValue(): float
    {
        return round(max(0, (float) $this->stock_on_hand) * (float) $this->unit_cost, 2);
    }

    /**
     * Items at or below their reorder level, including those already out.
     */
    #[Scope]
    protected function lowStock(Builder $query): void
    {
        $query->whereColumn('stock_on_hand', '<=', 'reorder_level');
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => InventoryCategory::class,
            'unit' => StockUnit::class,
            'stock_on_hand' => 'decimal:2',
            'reorder_level' => 'decimal:2',
            'unit_cost' => 'decimal:4',
            'is_active' => 'boolean',
        ];
    }
}
