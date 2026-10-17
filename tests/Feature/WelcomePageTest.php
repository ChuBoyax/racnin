<?php

namespace Tests\Feature;

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
