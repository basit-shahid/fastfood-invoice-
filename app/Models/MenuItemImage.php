<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class MenuItemImage extends Model
{
    protected $fillable = ['menu_item_id', 'mime_type', 'checksum', 'contents'];

    protected $hidden = ['contents'];

    public static function storeFor(MenuItem $item, UploadedFile $upload): void
    {
        $contents = $upload->getContent();
        $checksum = hash('sha256', $contents);
        static::updateOrCreate(['menu_item_id' => $item->id], [
            'mime_type' => $upload->getMimeType(),
            'checksum' => $checksum,
            'contents' => base64_encode($contents),
        ]);
        $item->update(['image' => 'db:'.$checksum]);
    }
}
