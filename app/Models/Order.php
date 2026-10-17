<?php

// app/Models/Order.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'subtotal',
        'tax',
        'discount',
        'total',
        'cash_received',
        'change_amount',
        'payment_method',
        'status',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'cash_received' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (! $order->invoice_number) {
                $order->invoice_number = self::generateInvoiceNumber();
            }
        });
    }

    public static function generateInvoiceNumber()
    {
        // Receipt identity must not depend on another checkout's uncommitted row.
        // Repeated products and identical orders are valid; only the receipt ID is unique.
        return 'INV-'.now()->format('Ymd').'-'.strtoupper((string) Str::ulid());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function calculateTotal()
    {
        $this->subtotal = $this->items()->sum('subtotal');
        $this->total = $this->subtotal + $this->tax - $this->discount;
        $this->save();
    }

    public function complete()
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();

        // Update daily report
        $this->updateDailyReport();
    }

    protected function updateDailyReport()
    {
        $dailyReport = DailyReport::firstOrCreate(
            ['date' => today()],
            [
                'total_orders' => 0,
                'total_revenue' => 0,
                'average_order_value' => 0,
            ]
        );

        $dailyReport->total_orders += 1;
        $dailyReport->total_revenue += $this->total;
        $dailyReport->average_order_value = $dailyReport->total_revenue / $dailyReport->total_orders;

        // Update top items
        $topItems = OrderItem::whereHas('order', function ($query) {
            $query->whereDate('created_at', today());
        })->select('menu_item_id', \DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('menu_item_id')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->with('menuItem')
            ->get();

        $dailyReport->top_items = $topItems->map(function ($item) {
            return [
                'name' => $item->menuItem->name,
                'quantity' => $item->total_quantity,
                'revenue' => $item->subtotal,
            ];
        });

        $dailyReport->save();
    }
}
