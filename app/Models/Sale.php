<?php

namespace App\Models;

use App\Enums\SaleChannel;
use Database\Factories\SaleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A completed sale. Sales are never edited or deleted; a mistake is undone by
 * voiding, which returns the used ingredients to stock.
 */
#[Fillable(['user_id', 'channel', 'reference', 'customer_name', 'total', 'notes', 'sold_at', 'voided_at', 'void_reason'])]
class Sale extends Model
{
    /** @use HasFactory<SaleFactory> */
    use HasFactory;

    /**
     * @return HasMany<SaleItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * @return HasMany<StockMovement, $this>
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isVoided(): bool
    {
        return $this->voided_at !== null;
    }

    /**
     * A short receipt number, e.g. "S-00042".
     *
     * @return Attribute<string, never>
     */
    protected function number(): Attribute
    {
        return Attribute::get(fn (): string => 'S-'.str_pad((string) $this->getKey(), 5, '0', STR_PAD_LEFT));
    }

    #[Scope]
    protected function completed(Builder $query): void
    {
        $query->whereNull('voided_at');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'channel' => SaleChannel::class,
            'total' => 'decimal:2',
            'sold_at' => 'datetime',
            'voided_at' => 'datetime',
        ];
    }
}
