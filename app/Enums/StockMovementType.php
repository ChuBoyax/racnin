<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum StockMovementType: string implements HasColor, HasIcon, HasLabel
{
    case Purchase = 'purchase';
    case Sale = 'sale';
    case SaleVoid = 'sale-void';
    case Waste = 'waste';
    case Adjustment = 'adjustment';

    public function getLabel(): string
    {
        return match ($this) {
            self::Purchase => 'Stock in',
            self::Sale => 'Sale',
            self::SaleVoid => 'Sale voided',
            self::Waste => 'Waste',
            self::Adjustment => 'Count adjustment',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Purchase => 'success',
            self::Sale => 'info',
            self::SaleVoid => 'gray',
            self::Waste => 'danger',
            self::Adjustment => 'warning',
        };
    }

    public function getIcon(): Heroicon
    {
        return match ($this) {
            self::Purchase => Heroicon::OutlinedArrowDownTray,
            self::Sale => Heroicon::OutlinedShoppingBag,
            self::SaleVoid => Heroicon::OutlinedArrowUturnLeft,
            self::Waste => Heroicon::OutlinedTrash,
            self::Adjustment => Heroicon::OutlinedAdjustmentsHorizontal,
        };
    }
}
