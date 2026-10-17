<?php

namespace App\Console\Commands;

use App\Http\Controllers\HomeController;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;

#[Signature('site:export {--output=dist : Folder to write the site to, relative to the project root} {--url= : Public site URL for absolute links, e.g. https://racnin.vercel.app}')]
#[Description('Render the landing page to static HTML for hosts without PHP, like Vercel')]
class ExportStaticSite extends Command
{
    /**
     * Stand-in root URL that is stripped afterwards, leaving root-relative links.
     */
    private const PLACEHOLDER_ROOT = 'https://racnin.static-export';

    /**
     * Files and folders under public/ that the static site needs.
     *
     * @var list<string>
     */
    private const PUBLIC_ASSETS = ['build', 'images', 'favicon.ico', 'robots.txt'];

    /**
     * Execute the console command.
     */
    public function handle(Filesystem $files, HomeController $home): int
    {
        $folder = trim(str_replace('\\', '/', (string) $this->option('output')), '/');
        $output = base_path($folder);

        if ($folder === '' || $folder === '.' || str_contains($folder, '..')) {
            $this->components->error('Choose a folder inside the project, like [dist].');

            return self::FAILURE;
        }

        // The folder gets replaced, so only ever touch an empty one or a previous export.
        if ($files->isDirectory($output) && ! $files->isEmptyDirectory($output) && ! $files->exists("{$output}/index.html")) {
            $this->components->error("[{$folder}] already has other files in it. Pick an empty folder.");

            return self::FAILURE;
        }

        if (! $files->exists(public_path('build/manifest.json'))) {
            $this->components->error('No production assets found. Run [npm run build] first.');

            return self::FAILURE;
        }

        // Always point at the compiled assets, even while `npm run dev` is running.
        Vite::useHotFile(storage_path('framework/vite-static-export.hot'));

        $siteUrl = rtrim((string) $this->option('url'), '/');
        $root = $siteUrl ?: self::PLACEHOLDER_ROOT;
        URL::forceRootUrl($root);
        URL::forceScheme(parse_url($root, PHP_URL_SCHEME) ?: 'https');

        $html = $home()->render();

        if ($siteUrl === '') {
            $html = str_replace([self::PLACEHOLDER_ROOT, str_replace('/', '\/', self::PLACEHOLDER_ROOT)], '', $html);
        }

        // Drop Blade's indentation; about a third of the page is leading whitespace.
        $html = preg_replace('/\n[ \t]+/', "\n", $html);

        $files->deleteDirectory($output);
        $files->ensureDirectoryExists($output);
        $files->put("{$output}/index.html", $html);

        foreach (self::PUBLIC_ASSETS as $asset) {
            $source = public_path($asset);

            match (true) {
                $files->isDirectory($source) => $files->copyDirectory($source, "{$output}/{$asset}"),
                $files->isFile($source) => $files->copy($source, "{$output}/{$asset}"),
                default => null,
            };
        }

        $this->components->info("Static site exported to [{$folder}].");

        return self::SUCCESS;
    }
}
