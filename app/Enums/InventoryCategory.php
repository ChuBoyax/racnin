<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum InventoryCategory: string implements HasColor, HasLabel
{
    case CoffeeAndTea = 'coffee-tea';
    case Dairy = 'dairy';
    case SyrupsAndMixers = 'syrups-mixers';
    case PowdersAndToppings = 'powders-toppings';
    case Food = 'food';
    case Packaging = 'packaging';

    public function getLabel(): string
    {
        return match ($this) {
            self::CoffeeAndTea => 'Coffee & tea',
            self::Dairy => 'Dairy',
            self::SyrupsAndMixers => 'Syrups & mixers',
            self::PowdersAndToppings => 'Powders & toppings',
            self::Food => 'Food',
            self::Packaging => 'Packaging',
        };
    }

    /**
     * @return array<int, string>
     */
    public function getColor(): array
    {
        return match ($this) {
            self::CoffeeAndTea => Color::Amber,
            self::Dairy => Color::Sky,
            self::SyrupsAndMixers => Color::Pink,
            self::PowdersAndToppings => Color::Violet,
            self::Food => Color::Lime,
            self::Packaging => Color::Stone,
        };
    }
}
