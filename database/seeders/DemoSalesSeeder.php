<?php

namespace Database\Seeders;

use App\Actions\RecordSale;
use App\Actions\RecordStockMovement;
use App\Actions\VoidSale;
use App\Enums\SaleChannel;
use App\Enums\StockMovementType;
use App\Models\InventoryItem;
use App\Models\ProductVariant;
use App\Models\RecipeItem;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Two weeks of made-up trading so the dashboard has something to show:
 * sales (bestsellers more often), deliveries, a little waste, and a couple
 * of voided mistakes. Everything goes through the same actions as the UI.
 */
class DemoSalesSeeder extends Seeder
{
    private const DAYS = 14;

    /**
     * Days ago => sku => [quantity, total paid].
     *
     * @var array<int, array<string, array{0: int, 1: int}>>
     */
    private const DELIVERIES = [
        8 => ['MILK-FRESH' => [12000, 1080], 'BEAN-ESP' => [2000, 1150], 'SODA-WATER' => [12000, 240], 'CUP-16' => [300, 660]],
        7 => ['CREAM-WHIP' => [700, 175], 'FRAPPE-BASE' => [800, 200]],
        5 => ['SIOMAI' => [200, 520], 'RICE' => [10000, 520], 'EGG' => [60, 480]],
        4 => ['FRIES' => [2000, 250]],
        2 => ['MILK-FRESH' => [8000, 680], 'JAM-STRAW' => [1000, 210]],
    ];

    /**
     * Days ago => sku => [quantity, reason].
     *
     * @var array<int, array<string, array{0: int, 1: string}>>
     */
    private const WASTE = [
        6 => ['MILK-FRESH' => [1000, 'Spoiled or expired']],
        3 => ['CUP-16' => [6, 'Spilled or damaged']],
    ];

    private const CUSTOMERS = ['Joy', 'Mark', 'Ana', 'Paolo', 'Rica', 'Jun', 'Bea', 'Carlo', 'Mae', 'Kyle', 'Ella', 'Migs'];

    public function run(RecordSale $recordSale, RecordStockMovement $recordStockMovement, VoidSale $voidSale): void
    {
        $user = User::first();
        $menu = $this->weightedMenu();
        $items = InventoryItem::all()->keyBy('sku');
        $recipes = RecipeItem::all()->groupBy('product_variant_id');

        for ($daysAgo = self::DAYS - 1; $daysAgo >= 0; $daysAgo--) {
            $day = today()->subDays($daysAgo);

            foreach (self::DELIVERIES[$daysAgo] ?? [] as $sku => [$quantity, $paid]) {
                $recordStockMovement->handle($items[$sku], StockMovementType::Purchase, $quantity, unitCost: $paid / $quantity, reference: 'DR-'.$day->format('md'), user: $user, occurredAt: $day->copy()->setTime(9, 0));
            }

            foreach (self::WASTE[$daysAgo] ?? [] as $sku => [$quantity, $reason]) {
                $recordStockMovement->handle($items[$sku], StockMovementType::Waste, -$quantity, notes: $reason, user: $user, occurredAt: $day->copy()->setTime(9, 30));
            }

            foreach ($this->saleTimes($day) as $soldAt) {
                $lines = $this->linesStockCanCover(
                    collect(range(1, fake()->randomElement([1, 1, 1, 2, 2, 3])))
                        ->map(fn (): array => ['product_variant_id' => fake()->randomElement($menu), 'quantity' => fake()->randomElement([1, 1, 1, 2])])
                        ->all(),
                    $recipes,
                );

                if ($lines === []) {
                    continue;
                }

                $isOnline = fake()->boolean(30);

                $recordSale->handle(
                    $lines,
                    channel: $isOnline ? SaleChannel::Online : SaleChannel::Counter,
                    reference: $isOnline ? 'RN-'.Str::upper(Str::random(5)) : null,
                    customerName: $isOnline ? fake()->randomElement(self::CUSTOMERS) : null,
                    user: $user,
                    soldAt: $soldAt,
                );
            }
        }

        Sale::inRandomOrder()->limit(2)->get()
            ->each(fn (Sale $sale): Sale => $voidSale->handle($sale, 'Entered by mistake', $user));
    }

    /**
     * Drops lines the shelf can't make, like staff would, so the demo never
     * sells stock it doesn't have.
     *
     * @param  list<array{product_variant_id: int, quantity: int}>  $lines
     * @param  Collection<int, Collection<int, RecipeItem>>  $recipes
     * @return list<array{product_variant_id: int, quantity: int}>
     */
    private function linesStockCanCover(array $lines, Collection $recipes): array
    {
        $onHand = InventoryItem::pluck('stock_on_hand', 'id')->map(fn (string $quantity): float => (float) $quantity);
        $accepted = [];

        foreach ($lines as $line) {
            $needs = $recipes->get($line['product_variant_id'], collect())
                ->mapWithKeys(fn (RecipeItem $recipeItem): array => [$recipeItem->inventory_item_id => (float) $recipeItem->quantity * $line['quantity']]);

            if ($needs->every(fn (float $quantity, int $itemId): bool => $onHand[$itemId] >= $quantity)) {
                $needs->each(fn (float $quantity, int $itemId) => $onHand[$itemId] -= $quantity);
                $accepted[] = $line;
            }
        }

        return $accepted;
    }

    /**
     * Variant ids, with the website's bestsellers and sulit picks repeated so
     * they sell more often, and large sizes a little less.
     *
     * @return list<int>
     */
    private function weightedMenu(): array
    {
        return ProductVariant::with('product')->get()
            ->flatMap(function (ProductVariant $variant): array {
                $badges = config("racnin.items.{$variant->product->slug}.badges", []);
                $weight = array_intersect($badges, ['bestseller', 'sulit', 'new']) ? 4 : 2;

                return array_fill(0, $variant->name === '22 oz' ? $weight - 1 : $weight, $variant->id);
            })
            ->values()
            ->all();
    }

    /**
     * Random times between opening and closing, busier on weekends. Today
     * only gets sales up to now.
     *
     * @return list<Carbon>
     */
    private function saleTimes(Carbon $day): array
    {
        $opens = $day->copy()->setTime(10, 0);
        $closes = $day->copy()->setTime(20, 45)->min(now());

        if ($closes->lessThanOrEqualTo($opens)) {
            return [];
        }

        $fullDay = $opens->diffInMinutes($day->copy()->setTime(20, 45));
        $minutesOpen = (int) $opens->diffInMinutes($closes);
        $count = (int) round(($day->isWeekend() ? fake()->numberBetween(16, 26) : fake()->numberBetween(10, 18)) * $minutesOpen / $fullDay);

        return collect(range(1, max($count, 1)))
            ->map(fn (): Carbon => $opens->copy()->addMinutes(fake()->numberBetween(0, $minutesOpen)))
            ->sort()
            ->values()
            ->all();
    }
}
