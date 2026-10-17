<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class WelcomePageTest extends TestCase
{
    public function test_it_lists_every_menu_item_with_its_starting_price(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();

        foreach (config('racnin.items') as $id => $item) {
            $response->assertSee('data-item="'.$id.'"', false);
            $response->assertSee($item['name']);
            $response->assertSee('₱'.reset($item['options']['choices']));
        }
    }

    public function test_it_shows_the_shop_details(): void
    {
        $shop = config('racnin.shop');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee($shop['name'])
            ->assertSee($shop['address']['street'])
            ->assertSee($shop['hours']['label'])
            ->assertSee($shop['email']);
    }

    public function test_the_homepage_sets_no_cookies_so_the_cdn_can_cache_it(): void
    {
        $response = $this->get(route('home'))->assertOk();

        $this->assertSame([], $response->headers->getCookies());
        $this->assertStringContainsString('public', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('s-maxage=86400', $response->headers->get('Cache-Control'));
    }

    public function test_links_stay_on_https_behind_vercels_proxy(): void
    {
        $this->get(route('home'), ['X-Forwarded-Proto' => 'https', 'X-Forwarded-Host' => 'racnin.vercel.app'])
            ->assertOk()
            ->assertSee('https://racnin.vercel.app/images/logo-mark.webp', false)
            ->assertSee('href="https://racnin.vercel.app/admin/login"', false);
    }

    public function test_the_footer_takes_staff_to_the_admin_login(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('href="'.route('filament.admin.auth.login').'"', false)
            ->assertSee('Staff login');
    }

    public function test_menu_pictures_are_images_instead_of_inline_drawings(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('src="'.route('menu-art', ['item' => 'cafe-mocha']).'?v=', false)
            ->assertDontSee('viewBox="0 0 200 220"', false);
    }

    public function test_a_combo_shows_the_pictures_of_the_items_it_is_made_of(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSeeInOrder([
                'data-item="combo-soda-siomai"',
                route('menu-art', ['item' => 'strawberry-soda']).'?v=',
                route('menu-art', ['item' => 'siomai-sa-tisa']).'?v=',
                'data-item="combo-coffee-siomai"',
            ], false);
    }

    public function test_a_photo_saved_for_an_item_replaces_its_drawing(): void
    {
        $folder = public_path('images/menu');
        $folderExisted = File::isDirectory($folder);
        File::ensureDirectoryExists($folder);
        File::put("{$folder}/nachos.png", base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='));

        try {
            $this->get(route('home'))
                ->assertOk()
                ->assertSee('src="'.asset('images/menu/nachos.png').'"', false)
                ->assertDontSee(route('menu-art', ['item' => 'nachos']), false);
        } finally {
            $folderExisted ? File::delete("{$folder}/nachos.png") : File::deleteDirectory($folder);
        }
    }

    public function test_search_engines_get_the_menus_price_range(): void
    {
        config(['racnin.items.nachos.options.choices' => ['Regular' => 99]]);

        $content = $this->get(route('home'))->assertOk()->getContent();

        preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $content, $matches);
        $this->assertSame('₱25–₱99', json_decode($matches[1] ?? '', true)['priceRange'] ?? null);
    }

    public function test_it_embeds_the_catalog_the_cart_prices_from(): void
    {
        config(['racnin.items.cafe-mocha.options.choices' => ['16 oz' => 41, '22 oz' => 52]]);

        $content = $this->get(route('home'))->assertOk()->getContent();

        preg_match('/<script type="application\/json" id="racnin-data">(.*?)<\/script>/s', $content, $matches);
        $payload = json_decode($matches[1] ?? '', true);

        $this->assertSame(['16 oz' => 41, '22 oz' => 52], $payload['items']['cafe-mocha']['choices']);
        $this->assertSame('Size', $payload['items']['cafe-mocha']['label']);
        $this->assertSame(config('racnin.shop.timezone'), $payload['shop']['timezone']);
    }
}
