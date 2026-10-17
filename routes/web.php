<?php

use App\Http\Controllers\HomeController;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

// The homepage is the same for every visitor, so it skips the session and
// lets Vercel's CDN cache it. Each deployment starts with a fresh cache.
Route::get('/', HomeController::class)
    ->name('home')
    ->withoutMiddleware([StartSession::class, ShareErrorsFromSession::class, PreventRequestForgery::class])
    ->middleware('cache.headers:public;max_age=300;s_maxage=86400;stale_while_revalidate=604800;etag');
