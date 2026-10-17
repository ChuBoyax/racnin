<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum SaleChannel: string implements HasColor, HasIcon, HasLabel
{
    case Counter = 'counter';
    case Online = 'online';

    public function getLabel(): string
    {
        return match ($this) {
            self::Counter => 'Walk-in',
            self::Online => 'Website order',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Counter => 'gray',
            self::Online => 'primary',
        };
    }

    public function getIcon(): Heroicon
    {
        return match ($this) {
            self::Counter => Heroicon::OutlinedBuildingStorefront,
            self::Online => Heroicon::OutlinedGlobeAlt,
        };
    }
}
