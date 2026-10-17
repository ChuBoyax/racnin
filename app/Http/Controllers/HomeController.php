<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Show the landing page with the hardcoded menu from config/racnin.php,
     * plus the footer's way into the inventory admin for staff.
     */
    public function __invoke(): View
    {
        return view('welcome', [
            'shop' => config('racnin.shop'),
            'categories' => config('racnin.categories'),
            'items' => config('racnin.items'),
            'staffLoginUrl' => route('filament.admin.auth.login'),
        ]);
    }
}
