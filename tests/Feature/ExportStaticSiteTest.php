<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ExportStaticSiteTest extends TestCase
{
    private const OUTPUT = 'storage/framework/testing/static-export';

    protected function tearDown(): void
    {
        File::deleteDirectory(base_path(self::OUTPUT));

        parent::tearDown();
    }

    public function test_it_writes_the_landing_page_with_root_relative_links(): void
    {
        $this->artisan('site:export', ['--output' => self::OUTPUT])->assertSuccessful();

        $html = File::get(base_path(self::OUTPUT.'/index.html'));

        $this->assertStringContainsString(config('racnin.shop.name'), $html);
        $this->assertStringContainsString('src="/images/logo-mark.webp"', $html);
        $this->assertMatchesRegularExpression('#href="/build/assets/app-[^"]+\.css"#', $html);
        $this->assertStringNotContainsString('static-export', $html);
        $this->assertFileExists(base_path(self::OUTPUT.'/build/manifest.json'));
        $this->assertFileExists(base_path(self::OUTPUT.'/images/logo-mark.webp'));
    }

    public function test_it_writes_the_menu_drawings_that_the_page_links_to(): void
    {
        $this->artisan('site:export', ['--output' => self::OUTPUT])->assertSuccessful();

        $this->assertStringContainsString('src="/menu-art/cafe-mocha.svg?v=', File::get(base_path(self::OUTPUT.'/index.html')));
        $this->assertStringStartsWith('<svg xmlns="http://www.w3.org/2000/svg"', File::get(base_path(self::OUTPUT.'/menu-art/cafe-mocha.svg')));
        $this->assertFileDoesNotExist(base_path(self::OUTPUT.'/menu-art/combo-soda-siomai.svg'));
    }

    public function test_it_uses_absolute_links_when_given_the_site_url(): void
    {
        $this->artisan('site:export', ['--output' => self::OUTPUT, '--url' => 'https://racnin.example'])->assertSuccessful();

        $html = File::get(base_path(self::OUTPUT.'/index.html'));

        $this->assertStringContainsString('content="https://racnin.example/images/logo-mark.png"', $html);
    }

    public function test_the_staff_login_button_is_left_out_when_there_is_no_login_page(): void
    {
        $this->artisan('site:export', ['--output' => self::OUTPUT])->assertSuccessful();

        $html = File::get(base_path(self::OUTPUT.'/index.html'));

        $this->assertStringNotContainsString('Staff login', $html);
        $this->assertStringNotContainsString('/admin/login', $html);
    }

    public function test_the_staff_login_button_points_at_the_given_login_page(): void
    {
        $this->artisan('site:export', ['--output' => self::OUTPUT, '--login-url' => 'https://admin.racnin.example/admin/login'])->assertSuccessful();

        $this->assertStringContainsString('href="https://admin.racnin.example/admin/login"', File::get(base_path(self::OUTPUT.'/index.html')));
    }

    public function test_it_refuses_to_replace_a_folder_that_is_not_an_export(): void
    {
        $this->artisan('site:export', ['--output' => '.'])->assertFailed();
        $this->artisan('site:export', ['--output' => 'app'])->assertFailed();
        $this->artisan('site:export', ['--output' => '../elsewhere'])->assertFailed();

        $this->assertDirectoryExists(app_path('Http'));
    }
}
