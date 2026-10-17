<?php

namespace App\Http\Controllers;

use App\View\Menu;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Show the landing page with the hardcoded menu from config/racnin.php,
     * plus the footer's way into the inventory admin for staff.
     */
    public function __invoke(Menu $menu): View
    {
        return view('home', [
            'shop' => config('racnin.shop'),
            'menu' => $menu,
            'staffLoginUrl' => route('filament.admin.auth.login'),
        ]);
    }
}
