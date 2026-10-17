<?php

namespace App\View;

use Illuminate\Support\Arr;
use InvalidArgumentException;

/**
 * The menu the website shows. It is hardcoded in config/racnin.php for now,
 * and pages and the order script only read it through this class, so it can
 * later come from the products in the database without touching them.
 */
class Menu
{
    /**
     * @param  array<string, array{name: string, category: string, description: string, options: array{label: ?string, choices: array<string, int>}, regular_price?: int, badges: list<string>, tint: string, art: array<string, mixed>}>  $items
     * @param  array<string, string>  $categories
     */
    public function __construct(
        private readonly array $items,
        private readonly array $categories,
    ) {}

    /**
     * The menu as written in config/racnin.php.
     */
    public static function fromConfig(): self
    {
        return new self(config('racnin.items'), config('racnin.categories'));
    }

    /**
     * Every item in menu order, keyed by its id.
     *
     * @return array<string, array{name: string, category: string, description: string, options: array{label: ?string, choices: array<string, int>}, regular_price?: int, badges: list<string>, tint: string, art: array<string, mixed>}>
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * @return array{name: string, category: string, description: string, options: array{label: ?string, choices: array<string, int>}, regular_price?: int, badges: list<string>, tint: string, art: array<string, mixed>}
     */
    public function item(string $id): array
    {
        return $this->items[$id] ?? throw new InvalidArgumentException("There is no [{$id}] on the menu.");
    }

    /**
     * Category names in menu order, keyed by their id.
     *
     * @return array<string, string>
     */
    public function categories(): array
    {
        return $this->categories;
    }

    /**
     * How many items there are, on the whole menu or in one category.
     */
    public function count(?string $category = null): int
    {
        if ($category === null) {
            return count($this->items);
        }

        return count(array_filter($this->items, fn (array $item): bool => $item['category'] === $category));
    }

    /**
     * The price of an item's first choice, which the site shows as "from ₱…".
     */
    public function startingPrice(string $id): int
    {
        return Arr::first($this->item($id)['options']['choices']);
    }

    /**
     * The cheapest and the priciest starting prices on the menu.
     *
     * @return array{0: int, 1: int}
     */
    public function priceRange(): array
    {
        $prices = array_map($this->startingPrice(...), array_keys($this->items));

        return [min($prices), max($prices)];
    }

    /**
     * Art for the items that have a drawing of their own, keyed by item id.
     * Combos have none, since they show the items they're made of.
     *
     * @return array<string, array<string, mixed>>
     */
    public function drawings(): array
    {
        $drawn = array_filter($this->items, fn (array $item): bool => $item['art']['type'] !== 'combo');

        return array_map(fn (array $item): array => $item['art'], $drawn);
    }

    /**
     * What the order script needs to price a cart, keyed by item id.
     *
     * @return array<string, array{name: string, category: string, tint: string, label: ?string, choices: array<string, int>}>
     */
    public function catalog(): array
    {
        return array_map(fn (array $item): array => [
            'name' => $item['name'],
            'category' => $item['category'],
            'tint' => $item['tint'],
            'label' => $item['options']['label'],
            'choices' => $item['options']['choices'],
        ], $this->items);
    }
}
