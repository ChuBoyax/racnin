<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * The same categories the website menu uses (config/racnin.php).
 */
enum MenuCategory: string implements HasColor, HasLabel
{
    case IcedCoffee = 'iced-coffee';
    case NonCoffee = 'non-coffee';
    case Frappe = 'frappe';
    case FruitSoda = 'fruit-soda';
    case RiceMeals = 'rice-meals';
    case Snacks = 'snacks';
    case Combos = 'combos';

    public function getLabel(): string
    {
        return match ($this) {
            self::IcedCoffee => 'Iced Coffee',
            self::NonCoffee => 'Non-Coffee',
            self::Frappe => 'Frappe',
            self::FruitSoda => 'Fruit Soda',
            self::RiceMeals => 'Rice Meals',
            self::Snacks => 'Snacks',
            self::Combos => 'Combos',
        };
    }

    /**
     * @return array<int, string>
     */
    public function getColor(): array
    {
        return match ($this) {
            self::IcedCoffee => Color::Amber,
            self::NonCoffee => Color::Pink,
            self::Frappe => Color::Orange,
            self::FruitSoda => Color::Lime,
            self::RiceMeals => Color::Yellow,
            self::Snacks => Color::Red,
            self::Combos => Color::Violet,
        };
    }
}
