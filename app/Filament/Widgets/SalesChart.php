<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Filament\Widgets\ChartWidget;

class SalesChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Sales';

    protected ?string $description = 'Completed sales per day, in pesos';

    protected ?string $maxHeight = '260px';

    public ?string $filter = '14';

    /**
     * @return array<string, string>
     */
    protected function getFilters(): ?array
    {
        return [
            '7' => 'Last 7 days',
            '14' => 'Last 14 days',
            '30' => 'Last 30 days',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $days = (int) ($this->filter ?? 14);
        $start = today()->subDays($days - 1);

        $totals = Sale::completed()
            ->where('sold_at', '>=', $start)
            ->get(['sold_at', 'total'])
            ->groupBy(fn (Sale $sale): string => $sale->sold_at->toDateString())
            ->map(fn ($sales): float => $sales->sum(fn (Sale $sale): float => (float) $sale->total));

        $dates = collect(range(0, $days - 1))->map(fn (int $offset) => $start->copy()->addDays($offset));

        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => $dates->map(fn ($date): float => round($totals->get($date->toDateString(), 0.0), 2))->all(),
                    'borderColor' => '#ee7f1a',
                    'backgroundColor' => 'rgba(238, 127, 26, 0.12)',
                    'pointBackgroundColor' => '#ee7f1a',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $dates->map(fn ($date): string => $date->format('M j'))->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => ['beginAtZero' => true],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
