<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Number;

/**
 * Stock is always counted in the smallest practical unit, so recipes never
 * need conversions: grams, milliliters or whole pieces.
 */
enum StockUnit: string implements HasLabel
{
    case Gram = 'g';
    case Milliliter = 'ml';
    case Piece = 'pc';

    public function getLabel(): string
    {
        return match ($this) {
            self::Gram => 'Grams (g)',
            self::Milliliter => 'Milliliters (ml)',
            self::Piece => 'Pieces (pcs)',
        };
    }

    public function abbreviation(): string
    {
        return match ($this) {
            self::Gram => 'g',
            self::Milliliter => 'ml',
            self::Piece => 'pcs',
        };
    }

    /**
     * Format a quantity with this unit, e.g. "1,250 g" or "2.5 ml".
     */
    public function format(float|int|string|null $quantity): string
    {
        return Number::format((float) $quantity, maxPrecision: 2).' '.$this->abbreviation();
    }
}
