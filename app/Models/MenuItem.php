<?php

// app/Models/MenuItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category',
        'image',
        'is_available',
        'preparation_time',
        'stock_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }
        if (str_starts_with($this->image, 'db:')) {
            return route('menu.image', ['menuItem' => $this->id, 'checksum' => substr($this->image, 3)]);
        }

        // Legacy files are served on the current host, regardless of a stale APP_URL.
        return url('/storage/'.ltrim($this->image, '/'));
    }

    public function getFormattedPriceAttribute()
    {
        return 'PKR '.number_format($this->price, 2);
    }
}
