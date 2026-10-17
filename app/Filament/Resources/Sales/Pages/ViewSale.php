<?php

namespace App\Filament\Resources\Sales\Pages;

use App\Filament\Resources\Sales\Actions\VoidSaleAction;
use App\Filament\Resources\Sales\SaleResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewSale extends ViewRecord
{
    protected static string $resource = SaleResource::class;

    public function getTitle(): string|Htmlable
    {
        return "Sale {$this->getRecord()->number}";
    }

    protected function getHeaderActions(): array
    {
        return [
            VoidSaleAction::make(),
        ];
    }
}
