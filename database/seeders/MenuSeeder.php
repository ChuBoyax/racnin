<?php

namespace Database\Seeders;

use App\Enums\MenuCategory;
use App\Models\InventoryItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Imports the website menu (config/racnin.php) as products, with a sample
 * recipe for every size and flavor. Recipes are per serving and use the
 * SKUs from InventorySeeder; adjust the amounts to match the real bar.
 */
class MenuSeeder extends Seeder
{
    /**
     * One 16 oz drink, or one order of food.
     *
     * @var array<string, array<string, int>>
     */
    private const RECIPES = [
        'cafe-mocha' => ['BEAN-ESP' => 14, 'MILK-FRESH' => 150, 'SAUCE-CHOC' => 25],
        'caramel-macchiato' => ['BEAN-ESP' => 14, 'MILK-FRESH' => 160, 'SYRUP-VAN' => 15, 'SAUCE-CARA' => 10],
        'spanish-latte' => ['BEAN-ESP' => 14, 'MILK-FRESH' => 150, 'MILK-COND' => 30],
        'dirty-matcha' => ['MATCHA' => 5, 'BEAN-ESP' => 14, 'MILK-FRESH' => 160],
        'strawberry-milk' => ['JAM-STRAW' => 35, 'MILK-FRESH' => 200],
        'ube-latte' => ['SYRUP-UBE' => 30, 'MILK-FRESH' => 200],
        'cookies-and-cream' => ['CRUMBS-COOKIE' => 15, 'SYRUP-VAN' => 10, 'MILK-FRESH' => 190],
        'choco-berry' => ['SAUCE-CHOC' => 20, 'JAM-STRAW' => 20, 'MILK-FRESH' => 180],
        'chocolate' => ['SAUCE-CHOC' => 35, 'MILK-FRESH' => 200],
        'caramel-frappe' => ['FRAPPE-BASE' => 30, 'MILK-FRESH' => 120, 'SAUCE-CARA' => 35, 'CREAM-WHIP' => 20],
        'chocolate-frappe' => ['FRAPPE-BASE' => 30, 'MILK-FRESH' => 120, 'SAUCE-CHOC' => 40, 'CREAM-WHIP' => 20],
        'strawberry-frappe' => ['FRAPPE-BASE' => 30, 'MILK-FRESH' => 120, 'JAM-STRAW' => 40, 'CREAM-WHIP' => 20],
        'cookies-and-cream-frappe' => ['FRAPPE-BASE' => 30, 'MILK-FRESH' => 130, 'CRUMBS-COOKIE' => 25, 'CREAM-WHIP' => 20],
        'mango-soda' => ['SYRUP-MANGO' => 30, 'SODA-WATER' => 250],
        'strawberry-soda' => ['SYRUP-STRAW' => 30, 'SODA-WATER' => 250],
        'green-apple-soda' => ['SYRUP-APPLE' => 30, 'SODA-WATER' => 250],
        'blueberry-soda' => ['SYRUP-BLUE' => 30, 'SODA-WATER' => 250],
        'lumpia-silog' => ['RICE' => 180, 'GARLIC' => 5, 'EGG' => 1, 'LUMPIA' => 2, 'OIL-COOK' => 20],
        'siomai-rice' => ['RICE' => 180, 'GARLIC' => 5, 'EGG' => 1, 'SIOMAI' => 3, 'OIL-CHILI' => 5, 'OIL-COOK' => 10],
        'buldak-carbonara' => ['NOODLE-BULDAK' => 1, 'CHEESE-SAUCE' => 20, 'BOWL' => 1],
        'siomai-sa-tisa' => ['SIOMAI' => 4, 'OIL-CHILI' => 10, 'CALAMANSI' => 1, 'BOWL' => 1],
        'fries' => ['FRIES' => 150, 'OIL-COOK' => 20, 'CARTON-FRIES' => 1],
        'nachos' => ['NACHO-CHIPS' => 80, 'CHEESE-SAUCE' => 40, 'JALAPENO' => 10, 'BOWL' => 1],
        'dried-veggies' => ['VEGGIE-PACK' => 1],
    ];

    /**
     * @var array<string, string>
     */
    private const FRIES_FLAVORS = ['Cheese' => 'PWD-CHEESE', 'Sour Cream' => 'PWD-SOURCREAM', 'BBQ' => 'PWD-BBQ'];

    /**
     * Each combo is a 16 oz drink (picked by the variant) plus a side.
     *
     * @var array<string, array{side: string, drinks: array<string, string>}>
     */
    private const COMBOS = [
        'combo-soda-siomai' => ['side' => 'siomai-sa-tisa', 'drinks' => ['Mango' => 'mango-soda', 'Strawberry' => 'strawberry-soda', 'Green Apple' => 'green-apple-soda', 'Blueberry' => 'blueberry-soda']],
        'combo-coffee-siomai' => ['side' => 'siomai-sa-tisa', 'drinks' => ['Cafe Mocha' => 'cafe-mocha', 'Caramel Macchiato' => 'caramel-macchiato', 'Spanish Latte' => 'spanish-latte']],
        'combo-frappe-fries' => ['side' => 'fries', 'drinks' => ['Caramel' => 'caramel-frappe', 'Chocolate' => 'chocolate-frappe', 'Strawberry' => 'strawberry-frappe', 'Cookies & Cream' => 'cookies-and-cream-frappe']],
    ];

    private const LARGE_SIZE = '22 oz';

    private const LARGE_SCALE = 1.4;

    public function run(): void
    {
        $inventory = InventoryItem::pluck('id', 'sku');

        foreach (array_keys(config('racnin.items')) as $position => $slug) {
            $item = config("racnin.items.{$slug}");

            $product = Product::create([
                'name' => $item['name'],
                'slug' => $slug,
                'category' => $item['category'],
                'description' => $item['description'],
                'sort_order' => $position,
            ]);

            foreach (array_keys($item['options']['choices']) as $index => $choice) {
                $variant = $product->variants()->create([
                    'name' => $choice,
                    'price' => $item['options']['choices'][$choice],
                    'sort_order' => $index,
                ]);

                foreach ($this->recipeFor($slug, $choice) as $sku => $quantity) {
                    $variant->recipeItems()->create([
                        'inventory_item_id' => $inventory[$sku],
                        'quantity' => $quantity,
                    ]);
                }
            }
        }
    }

    /**
     * @return array<string, float|int>
     */
    private function recipeFor(string $slug, string $variant): array
    {
        if (isset(self::COMBOS[$slug])) {
            $combo = self::COMBOS[$slug];

            return $this->combine($this->recipeFor($combo['drinks'][$variant], '16 oz'), $this->recipeFor($combo['side'], 'Cheese'));
        }

        $recipe = self::RECIPES[$slug] ?? [];

        if ($slug === 'fries') {
            $recipe[self::FRIES_FLAVORS[$variant] ?? 'PWD-CHEESE'] = 5;
        }

        $category = MenuCategory::from(config("racnin.items.{$slug}.category"));

        if (! in_array($category, [MenuCategory::IcedCoffee, MenuCategory::NonCoffee, MenuCategory::Frappe, MenuCategory::FruitSoda], true)) {
            return $recipe;
        }

        $isLarge = $variant === self::LARGE_SIZE;

        if ($isLarge) {
            $recipe = array_map(fn (int|float $quantity): float => round($quantity * self::LARGE_SCALE), $recipe);
        }

        $packaging = $category === MenuCategory::Frappe
            ? ['LID-DOME' => 1, 'STRAW-WIDE' => 1, 'STICKER' => 1]
            : ['SEAL-FILM' => 1, 'STRAW' => 1, 'STICKER' => 1];

        return $recipe + [$isLarge ? 'CUP-22' : 'CUP-16' => 1] + $packaging;
    }

    /**
     * @param  array<string, float|int>  ...$recipes
     * @return array<string, float|int>
     */
    private function combine(array ...$recipes): array
    {
        $combined = [];

        foreach ($recipes as $recipe) {
            foreach ($recipe as $sku => $quantity) {
                $combined[$sku] = ($combined[$sku] ?? 0) + $quantity;
            }
        }

        return $combined;
    }
}
