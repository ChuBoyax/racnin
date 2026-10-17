<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Show the landing page with the hardcoded menu from config/racnin.php.
     */
    public function __invoke(): View
    {
        return view('welcome', [
            'shop' => config('racnin.shop'),
            'categories' => config('racnin.categories'),
            'items' => config('racnin.items'),
        ]);
    }
}
