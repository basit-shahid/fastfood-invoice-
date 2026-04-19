<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_name',
        'quantity',
        'unit',
        'last_updated_by',
    ];

    public function updater()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }
}
