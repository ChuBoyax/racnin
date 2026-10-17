<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\InventoryItems\InventoryItemResource;
use App\Filament\Resources\Sales\SaleResource;
use App\Models\InventoryItem;
use App\Models\Sale;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class ShopOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $daily = $this->dailySales(7);
        $today = $daily->last();
        $yesterday = $daily->get($daily->count() - 2, 0.0);
        $todayCount = Sale::completed()->whereDate('sold_at', today())->count();

        $items = InventoryItem::active()->get();
        $lowStock = $items->filter->isLowStock()->count();
        $outOfStock = $items->filter->isOutOfStock()->count();

        return [
            Stat::make('Sales today', $this->peso($today))
                ->description($todayCount.' '.Str::plural('sale', $todayCount).' · '.$this->comparison($today, $yesterday))
                ->descriptionIcon($today >= $yesterday ? Heroicon::ArrowTrendingUp : Heroicon::ArrowTrendingDown)
                ->color($today >= $yesterday ? 'success' : 'danger')
                ->chart($daily->all())
                ->url(SaleResource::getUrl('index', ['tab' => 'today'])),
            Stat::make('Last 7 days', $this->peso($daily->sum()))
                ->description('About '.$this->peso($daily->avg()).' a day')
                ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                ->color('primary'),
            Stat::make('Stock value', $this->peso($items->sum->stockValue()))
                ->description($items->count().' active stock items')
                ->descriptionIcon(Heroicon::OutlinedCube),
            Stat::make('Low on stock', (string) $lowStock)
                ->description($outOfStock > 0 ? "{$outOfStock} out of stock" : 'Nothing has run out')
                ->descriptionIcon(Heroicon::OutlinedExclamationTriangle)
                ->color(match (true) {
                    $outOfStock > 0 => 'danger',
                    $lowStock > 0 => 'warning',
                    default => 'success',
                })
                ->url(InventoryItemResource::getUrl('index', ['tab' => 'low-stock'])),
        ];
    }

    /**
     * Completed sales per day, oldest first, ending today.
     *
     * @return Collection<int, float>
     */
    private function dailySales(int $days): Collection
    {
        $start = today()->subDays($days - 1);

        $totals = Sale::completed()
            ->where('sold_at', '>=', $start)
            ->get(['sold_at', 'total'])
            ->groupBy(fn (Sale $sale): string => $sale->sold_at->toDateString())
            ->map(fn ($sales): float => $sales->sum(fn (Sale $sale): float => (float) $sale->total));

        return collect(range(0, $days - 1))
            ->map(fn (int $offset): float => round($totals->get($start->copy()->addDays($offset)->toDateString(), 0.0), 2));
    }

    private function comparison(float $today, float $yesterday): string
    {
        if ($yesterday <= 0) {
            return 'none yesterday';
        }

        $change = (int) round(($today - $yesterday) / $yesterday * 100);

        return ($change >= 0 ? "+{$change}%" : "{$change}%").' vs yesterday';
    }

    private function peso(float|int|null $amount): string
    {
        return Number::currency((float) $amount, 'PHP', 'en_PH', precision: 0);
    }
}
