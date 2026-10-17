<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Flat, ink-outlined illustrations of menu items, drawn in the same style as
 * the RacNin logo so the menu needs no photography to look consistent.
 */
class MenuArt extends Component
{
    private const CUP_TOP = 64;

    private const CUP_BASE = 196;

    private const CUP_LEFT = 52;

    private const CUP_TAPER = 9.5;

    private const LIQUID_BOTTOM = 201;

    /**
     * Speckle positions for cookie crumbs, spread across the cup.
     *
     * @var list<array{0: int, 1: int}>
     */
    private const SPECKLES = [
        [66, 178], [80, 160], [94, 192], [118, 182], [131, 158], [104, 140],
        [72, 124], [127, 112], [88, 106], [112, 96], [70, 194], [132, 190],
        [98, 166], [84, 184], [121, 136], [106, 118], [62, 146], [138, 132],
    ];

    /**
     * @param  array{type: string, layers?: list<array{0: string, 1: float}>, speckle?: string, whip?: string, drizzle?: string, side?: string, parts?: list<array<string, mixed>>}  $art
     */
    public function __construct(public array $art) {}

    /**
     * Whether the illustration is one of the cup drinks.
     */
    public function isDrink(): bool
    {
        return in_array($this->art['type'], ['iced', 'soda', 'frappe'], true);
    }

    /**
     * Liquid bands for a drink, ordered so each lower layer paints over the
     * one above it, leaving its curved top edge as the visible boundary.
     *
     * @return list<array{d: string, fill: string}>
     */
    public function liquid(): array
    {
        $surface = $this->liquidSurface();
        $depth = self::LIQUID_BOTTOM - $surface;
        $layerTop = self::LIQUID_BOTTOM;
        $bands = [];

        foreach ($this->art['layers'] ?? [] as [$color, $share]) {
            $layerTop -= $depth * $share;
            $bands[] = ['fill' => $color, 'top' => max($layerTop, $surface)];
        }

        $bands = array_reverse($bands);

        return array_map(fn (array $band, int $index): array => [
            'fill' => $band['fill'],
            'd' => $this->bandPath($band['top'], $index === 0 ? 2.5 : 8),
        ], $bands, array_keys($bands));
    }

    /**
     * Crumb dots that sit below the liquid surface.
     *
     * @return list<array{0: int, 1: int}>
     */
    public function speckles(): array
    {
        if (! isset($this->art['speckle'])) {
            return [];
        }

        $surface = $this->liquidSurface() + 6;

        return array_values(array_filter(self::SPECKLES, fn (array $point): bool => $point[1] > $surface));
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.menu-art');
    }

    private function liquidSurface(): int
    {
        return match ($this->art['type']) {
            'frappe' => 70,
            'soda' => 88,
            default => 84,
        };
    }

    private function bandPath(float $top, float $sag): string
    {
        $base = self::CUP_BASE;
        $bottom = self::LIQUID_BOTTOM;

        return sprintf(
            'M%.1f %.1f Q100 %.1f %.1f %.1f L%.1f %d Q%.1f %d %.1f %d L%.1f %d Q%.1f %d %.1f %d Z',
            $this->leftWall($top), $top,
            $top + $sag, $this->rightWall($top), $top,
            $this->rightWall($base), $base,
            $this->rightWall($base) - 0.5, $bottom, $this->rightWall($base) - 7, $bottom,
            $this->leftWall($base) + 7, $bottom,
            $this->leftWall($base) + 0.5, $bottom, $this->leftWall($base), $base,
        );
    }

    private function leftWall(float $y): float
    {
        return self::CUP_LEFT + ($y - self::CUP_TOP) * self::CUP_TAPER / (self::CUP_BASE - self::CUP_TOP);
    }

    private function rightWall(float $y): float
    {
        return 200 - $this->leftWall($y);
    }
}
