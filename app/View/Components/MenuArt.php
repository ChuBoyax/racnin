<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Product renders of menu items: clear plastic cups in perspective with
 * layered drinks, ice and condensation, plus shaded food. Shared gradients,
 * clip paths and filters live once per page in <x-menu-art-defs />.
 */
class MenuArt extends Component
{
    public const RIM_Y = 50;

    public const RIM_RX = 49;

    public const RIM_RY = 9;

    public const BASE_Y = 202;

    public const BASE_RX = 37;

    public const BASE_RY = 6.5;

    private int $seed;

    /**
     * @param  array{type: string, layers?: list<array{0: string, 1: float}>, speckle?: string, whip?: string, drizzle?: string, side?: string, parts?: list<array<string, mixed>>}  $art
     */
    public function __construct(public array $art)
    {
        $this->seed = crc32(json_encode($art)) & 0x7FFFFFFF;
    }

    /**
     * Whether the illustration is one of the cup drinks.
     */
    public function isDrink(): bool
    {
        return in_array($this->art['type'], ['iced', 'soda', 'frappe'], true);
    }

    /**
     * Outline of the whole cup, used for its plastic body and as a clip path.
     */
    public static function cupPath(): string
    {
        $base = self::BASE_Y;

        return sprintf(
            'M%s %s L%s %s A%s %s 0 0 0 %s %s L%s %s A%s %s 0 0 0 %s %s Z',
            100 - self::RIM_RX, self::RIM_Y,
            100 - self::BASE_RX, $base, self::BASE_RX, self::BASE_RY, 100 + self::BASE_RX, $base,
            100 + self::RIM_RX, self::RIM_Y, self::RIM_RX, self::RIM_RY, 100 - self::RIM_RX, self::RIM_Y,
        );
    }

    /**
     * Height of the liquid's surface inside the cup.
     */
    public function surfaceY(): float
    {
        return match ($this->art['type']) {
            'frappe' => 54,
            'soda' => 80,
            default => 76,
        };
    }

    /**
     * The liquid from its surface down to the base, including the far half
     * of the surface ellipse so the surface sits flush on top.
     */
    public function liquidPath(): string
    {
        $top = $this->surfaceY();

        return sprintf(
            'M%.1f %.1f L%.1f %s A%s %s 0 0 0 %.1f %s L%.1f %.1f A%.1f %.1f 0 0 0 %.1f %.1f Z',
            $this->left($top), $top,
            100 - self::BASE_RX, self::BASE_Y, self::BASE_RX, self::BASE_RY, 100 + self::BASE_RX, self::BASE_Y,
            $this->right($top), $top, $this->halfWidth($top), $this->ellipseHeight($top), $this->left($top), $top,
        );
    }

    /**
     * The visible top of the liquid.
     *
     * @return array{cx: int, cy: float, rx: float, ry: float, fill: string}
     */
    public function surface(): array
    {
        $top = $this->surfaceY();
        $layers = $this->art['layers'] ?? [['#ffffff', 1]];

        return [
            'cx' => 100,
            'cy' => $top,
            'rx' => round($this->halfWidth($top), 1),
            'ry' => round($this->ellipseHeight($top), 1),
            'fill' => $this->mix(end($layers)[0], '#ffffff', 0.18),
        ];
    }

    /**
     * Lower liquid layers, painted over the top layer from their curved
     * boundary down to the base. Listed top to bottom so each one overlaps
     * the layer above it.
     *
     * @return list<array{d: string, fill: string}>
     */
    public function bands(): array
    {
        $bands = [];

        foreach ($this->boundaries() as ['y' => $y, 'below' => $color]) {
            $bands[] = [
                'fill' => $color,
                'd' => sprintf(
                    'M%.1f %.1f A%.1f %.1f 0 0 0 %.1f %.1f L%.1f %s A%s %s 0 0 1 %.1f %s Z',
                    $this->left($y), $y, $this->halfWidth($y), $this->ellipseHeight($y), $this->right($y), $y,
                    100 + self::BASE_RX, self::BASE_Y, self::BASE_RX, self::BASE_RY, 100 - self::BASE_RX, self::BASE_Y,
                ),
            ];
        }

        return $bands;
    }

    /**
     * Wisps of each upper layer sinking into the one below, like espresso
     * settling into milk. They're blurred together with the bands.
     *
     * @return list<array{d: string, stroke: string, width: float}>
     */
    public function tendrils(): array
    {
        $random = $this->random();
        $tendrils = [];

        foreach ($this->boundaries() as ['y' => $y, 'above' => $color]) {
            for ($i = 0; $i < 3; $i++) {
                $x = $this->left($y) + 12 + $random() * ($this->halfWidth($y) * 2 - 24);
                $start = $y + $this->ellipseHeight($y) * 0.5;
                $depth = 6 + $random() * 12;
                $sway = ($random() - 0.5) * 22;

                $tendrils[] = [
                    'stroke' => $color,
                    'width' => round(7 + $random() * 6, 1),
                    'd' => sprintf('M%.1f %.1f c%.1f %.1f %.1f %.1f %.1f %.1f', $x, $start, $sway, $depth * 0.4, -$sway, $depth * 0.7, $sway * 0.5, $depth),
                ];
            }
        }

        return $tendrils;
    }

    /**
     * Ice cubes floating at the surface.
     *
     * @return list<array{x: float, y: float, size: float, rotate: int, opacity: float}>
     */
    public function ice(): array
    {
        if ($this->art['type'] === 'frappe') {
            return [];
        }

        $top = $this->surfaceY();

        return [
            ['x' => 66, 'y' => $top - 5, 'size' => 22, 'rotate' => -14, 'opacity' => 0.9],
            ['x' => 93, 'y' => $top - 9, 'size' => 25, 'rotate' => 9, 'opacity' => 0.95],
            ['x' => 117, 'y' => $top - 3, 'size' => 20, 'rotate' => -5, 'opacity' => 0.85],
            ['x' => 78, 'y' => $top + 17, 'size' => 21, 'rotate' => 21, 'opacity' => 0.55],
            ['x' => 108, 'y' => $top + 22, 'size' => 19, 'rotate' => -24, 'opacity' => 0.45],
        ];
    }

    /**
     * Condensation on the outside of a cold cup.
     *
     * @return list<array{x: float, y: float, r: float, drip: float}>
     */
    public function droplets(): array
    {
        $random = $this->random(7);
        $droplets = [];

        for ($i = 0; $i < 30; $i++) {
            $y = self::RIM_Y + 16 + $random() * (self::BASE_Y - self::RIM_Y - 26);
            $x = $this->left($y) + 4 + $random() * ($this->halfWidth($y) * 2 - 8);
            $big = $random() > 0.86;

            $droplets[] = [
                'x' => round($x, 1),
                'y' => round($y, 1),
                'r' => round($big ? 1.9 + $random() * 1.1 : 0.6 + $random() * 0.9, 1),
                'drip' => $big && $random() > 0.5 ? round(4 + $random() * 8, 1) : 0,
            ];
        }

        return $droplets;
    }

    /**
     * Rising bubbles for fruit sodas.
     *
     * @return list<array{x: float, y: float, r: float}>
     */
    public function bubbles(): array
    {
        if ($this->art['type'] !== 'soda') {
            return [];
        }

        return $this->scatter(30, 0.6, 2.1, 11);
    }

    /**
     * Cookie crumbs suspended in the drink.
     *
     * @return list<array{x: float, y: float, r: float}>
     */
    public function speckles(): array
    {
        return isset($this->art['speckle']) ? $this->scatter(46, 0.6, 1.9, 23) : [];
    }

    /**
     * Seeded points spread over an ellipse, for food textures like rice
     * grains, blisters and crumbs.
     *
     * @return list<array{x: float, y: float, rotate: int, size: float}>
     */
    public function points(int $count, float $cx, float $cy, float $rx, float $ry, int $salt = 0): array
    {
        $random = $this->random($salt + 101);
        $points = [];

        for ($i = 0; $i < $count; $i++) {
            $angle = $random() * 2 * M_PI;
            $distance = sqrt($random());

            $points[] = [
                'x' => round($cx + cos($angle) * $distance * $rx, 1),
                'y' => round($cy + sin($angle) * $distance * $ry, 1),
                'rotate' => (int) round($random() * 180),
                'size' => round($random(), 2),
            ];
        }

        return $points;
    }

    /**
     * Straw as a start point, length and angle, so a shaded rect can be
     * rotated into place.
     *
     * @return array{x: int, y: int, length: float, angle: float}
     */
    public function straw(): array
    {
        [$x1, $y1, $x2, $y2] = $this->art['type'] === 'frappe' ? [126, -2, 108, 190] : [128, 0, 108, 194];

        return [
            'x' => $x1,
            'y' => $y1,
            'length' => round(hypot($x2 - $x1, $y2 - $y1), 1),
            'angle' => round(rad2deg(atan2($x1 - $x2, $y2 - $y1)), 2),
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.menu-art');
    }

    /**
     * Each place where one liquid layer meets the next, top to bottom.
     *
     * @return list<array{y: float, above: string, below: string}>
     */
    private function boundaries(): array
    {
        $layers = $this->art['layers'] ?? [];
        $top = $this->surfaceY();
        $depth = self::BASE_Y - $top;
        $y = self::BASE_Y;
        $boundaries = [];

        foreach ($layers as $index => [$color, $share]) {
            $y -= $depth * $share;

            if (isset($layers[$index + 1])) {
                $boundaries[] = ['y' => $y, 'above' => $layers[$index + 1][0], 'below' => $color];
            }
        }

        return array_reverse($boundaries);
    }

    /**
     * Random points inside the liquid.
     *
     * @return list<array{x: float, y: float, r: float}>
     */
    private function scatter(int $count, float $minRadius, float $maxRadius, int $salt): array
    {
        $random = $this->random($salt);
        $top = $this->surfaceY() + 6;
        $points = [];

        for ($i = 0; $i < $count; $i++) {
            $y = $top + $random() * (self::BASE_Y - $top - 6);
            $x = $this->left($y) + 3 + $random() * ($this->halfWidth($y) * 2 - 6);
            $points[] = ['x' => round($x, 1), 'y' => round($y, 1), 'r' => round($minRadius + $random() * ($maxRadius - $minRadius), 1)];
        }

        return $points;
    }

    /**
     * A small seeded generator, so every render of an item looks the same.
     */
    private function random(int $salt = 0): Closure
    {
        $state = ($this->seed + $salt * 7919) & 0x7FFFFFFF;

        return function () use (&$state): float {
            $state = ($state * 1103515245 + 12345) & 0x7FFFFFFF;

            return $state / 0x7FFFFFFF;
        };
    }

    private function halfWidth(float $y): float
    {
        return self::RIM_RX - ($y - self::RIM_Y) * (self::RIM_RX - self::BASE_RX) / (self::BASE_Y - self::RIM_Y);
    }

    private function ellipseHeight(float $y): float
    {
        return self::RIM_RY - ($y - self::RIM_Y) * (self::RIM_RY - self::BASE_RY) / (self::BASE_Y - self::RIM_Y);
    }

    private function left(float $y): float
    {
        return 100 - $this->halfWidth($y);
    }

    private function right(float $y): float
    {
        return 100 + $this->halfWidth($y);
    }

    private function mix(string $color, string $with, float $amount): string
    {
        $from = sscanf(ltrim($color, '#'), '%02x%02x%02x');
        $to = sscanf(ltrim($with, '#'), '%02x%02x%02x');

        return vsprintf('#%02x%02x%02x', array_map(
            fn (int $a, int $b): int => (int) round($a + ($b - $a) * $amount),
            $from,
            $to,
        ));
    }
}
