<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuArtController;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

// The website is the same for every visitor, so it skips the session and
// lets Vercel's CDN cache it. Each deployment starts with a fresh cache.
Route::withoutMiddleware([StartSession::class, ShareErrorsFromSession::class, PreventRequestForgery::class])->group(function (): void {
    Route::get('/', HomeController::class)
        ->name('home')
        ->middleware('cache.headers:public;max_age=300;s_maxage=86400;stale_while_revalidate=604800;etag');

    // Drawing URLs carry a version (see MenuArtDrawing::url), so each one can be cached for good.
    Route::get('/menu-art/{item}.svg', MenuArtController::class)
        ->name('menu-art')
        ->where('item', '[a-z0-9-]+')
        ->middleware('cache.headers:public;max_age=31536000;s_maxage=31536000;immutable;etag');
});
