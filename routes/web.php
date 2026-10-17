<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome', [
        'shop' => config('racnin.shop'),
        'categories' => config('racnin.categories'),
        'items' => config('racnin.items'),
    ]);
})->name('home');
