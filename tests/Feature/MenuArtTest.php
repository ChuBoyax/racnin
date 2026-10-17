<?php

namespace Tests\Feature;

use App\View\MenuArtDrawing;
use Tests\TestCase;

class MenuArtTest extends TestCase
{
    public function test_every_menu_drawing_renders_as_an_svg_image(): void
    {
        $drawn = array_filter(config('racnin.items'), fn (array $item): bool => $item['art']['type'] !== 'combo');

        foreach (array_keys($drawn) as $item) {
            $this->get(route('menu-art', ['item' => $item]))
                ->assertOk()
                ->assertHeader('Content-Type', 'image/svg+xml')
                ->assertSee('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 220"', false);
        }
    }

    public function test_drawings_can_be_kept_by_browsers_and_the_cdn_for_a_year(): void
    {
        $response = $this->get(route('menu-art', ['item' => 'cafe-mocha']))->assertOk();

        $this->assertSame([], $response->headers->getCookies());
        $this->assertSame('immutable, max-age=31536000, public, s-maxage=31536000', $response->headers->get('Cache-Control'));
    }

    public function test_drawings_carry_the_logo_inside_because_an_svg_image_cannot_load_other_files(): void
    {
        $this->get(route('menu-art', ['item' => 'fries']))
            ->assertOk()
            ->assertSee('href="data:image/webp;base64,', false)
            ->assertDontSee('href="http', false);
    }

    public function test_returns_404_for_combos_and_items_not_on_the_menu(): void
    {
        $this->get(route('menu-art', ['item' => 'combo-soda-siomai']))->assertNotFound();
        $this->get(route('menu-art', ['item' => 'halo-halo']))->assertNotFound();
    }

    public function test_a_drawing_moves_to_a_new_url_when_its_art_changes(): void
    {
        $art = config('racnin.items.cafe-mocha.art');
        $recolored = ['layers' => [['#4a2716', 0.5], ['#e2c3a0', 0.5]]] + $art;

        $url = MenuArtDrawing::url('cafe-mocha', $art);

        $this->assertSame($url, MenuArtDrawing::url('cafe-mocha', $art));
        $this->assertNotSame($url, MenuArtDrawing::url('cafe-mocha', $recolored));
        $this->assertStringStartsWith(route('menu-art', ['item' => 'cafe-mocha']).'?v=', $url);
    }
}
