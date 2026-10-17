<?php

namespace App\Http\Controllers;

use App\View\Menu;
use App\View\MenuArtDrawing;
use Illuminate\Http\Response;

class MenuArtController extends Controller
{
    /**
     * Serve a menu item's drawing as an SVG image.
     */
    public function __invoke(Menu $menu, string $item): Response
    {
        $art = $menu->drawings()[$item] ?? abort(404);

        return response((new MenuArtDrawing($art))->toSvg(), headers: ['Content-Type' => 'image/svg+xml']);
    }
}
