<?php

namespace App\Filament\Resources\Sales\Pages;

use App\Filament\Resources\Sales\SaleResource;
use App\Models\Sale;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSales extends ListRecords
{
    protected static string $resource = SaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Record a sale'),
        ];
    }

    /**
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'today' => Tab::make('Today')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereDate('sold_at', today()))
                ->badge(fn (): int => Sale::completed()->whereDate('sold_at', today())->count()),
            'this-week' => Tab::make('This week')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('sold_at', '>=', now()->startOfWeek())),
        ];
    }
}
