<?php

namespace App\Filament\Widgets;

use App\Models\SaleItem;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;

class BestSellersChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Best sellers';

    protected ?string $description = 'Units sold in the last 7 days';

    protected ?string $maxHeight = '260px';

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $rows = SaleItem::query()
            ->whereHas('sale', fn (Builder $query) => $query->completed()->where('sold_at', '>=', today()->subDays(6)))
            ->selectRaw('product_name, SUM(quantity) as sold')
            ->groupBy('product_name')
            ->orderByDesc('sold')
            ->limit(6)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Sold',
                    'data' => $rows->map(fn (SaleItem $row): int => (int) $row->getAttribute('sold'))->all(),
                    'backgroundColor' => ['#ee7f1a', '#f7a826', '#a8a754', '#c9361e', '#7a4a2b', '#d9b26a'],
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $rows->pluck('product_name')->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => ['display' => false],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
