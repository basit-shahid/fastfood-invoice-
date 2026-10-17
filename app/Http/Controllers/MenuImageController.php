<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\MenuItemImage;
use Illuminate\Http\Request;

class MenuImageController extends Controller
{
    public function show(Request $request, MenuItem $menuItem, string $checksum)
    {
        abort_unless($menuItem->image === 'db:'.$checksum, 404);
        $image = MenuItemImage::where('menu_item_id', $menuItem->id)
            ->where('checksum', $checksum)->firstOrFail();
        $contents = base64_decode($image->contents, true);
        abort_if($contents === false, 404);

        $response = response($contents)->header('Content-Type', $image->mime_type)
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('Cache-Control', 'public, max-age=31536000, immutable')
            ->setEtag($checksum);
        $response->isNotModified($request);

        return $response;
    }
}
