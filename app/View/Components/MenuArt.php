<?php

namespace App\View\Components;

use App\View\Menu;
use App\View\MenuArtDrawing;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * A menu item's picture, as images the browser loads lazily and draws once.
 * A real photo in public/images/menu (ideally a transparent cutout) replaces
 * the drawing, and a combo shows the items it's made of side by side.
 */
class MenuArt extends Component
{
    /**
     * One picture, or two for a combo.
     *
     * @var list<array{src: string, width: int, height: int, class: string}>
     */
    public array $pictures;

    public function __construct(Menu $menu, public string $item, public string $loading = 'lazy')
    {
        $art = $menu->item($item)['art'];
        $parts = $art['type'] === 'combo' ? $art['parts'] : [$item];

        $this->pictures = ($photo = $this->photo($item)) !== null
            ? [$photo]
            : array_map(fn (string $part): array => $this->photo($part) ?? $this->drawing($part, $menu->item($part)['art']), $parts);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.menu-art');
    }

    /**
     * @return array{src: string, width: int, height: int, class: string}|null
     */
    private function photo(string $item): ?array
    {
        foreach (['webp', 'png', 'jpg'] as $extension) {
            $path = "images/menu/{$item}.{$extension}";

            if (is_file(public_path($path))) {
                [$width, $height] = getimagesize(public_path($path));

                return ['src' => asset($path), 'width' => $width, 'height' => $height, 'class' => 'menu-photo'];
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $art
     * @return array{src: string, width: int, height: int, class: string}
     */
    private function drawing(string $item, array $art): array
    {
        return ['src' => MenuArtDrawing::url($item, $art), 'width' => 200, 'height' => 220, 'class' => 'menu-art'];
    }
}
