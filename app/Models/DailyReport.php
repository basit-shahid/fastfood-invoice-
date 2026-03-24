<?php
// app/Models/DailyReport.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'total_orders',
        'total_revenue',
        'average_order_value',
        'top_items',
        'hourly_breakdown',
    ];

    protected $casts = [
        'date' => 'date',
        'total_revenue' => 'decimal:2',
        'average_order_value' => 'decimal:2',
        'top_items' => 'array',
        'hourly_breakdown' => 'array',
    ];
}