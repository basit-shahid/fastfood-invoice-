<?php

// app/Http/Controllers/OrderController.php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function create()
    {
        $menuItems = MenuItem::where('is_available', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $categories = $menuItems->groupBy('category');

        return view('cashier.pos', compact('menuItems', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            // Intentionally no "distinct" rule: the same product may appear on multiple lines.
            'items.*.id' => 'required|integer|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.instructions' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:cash,card,online',
            'cash_received' => 'required_if:payment_method,cash|nullable|numeric|min:0|max:99999999.99|decimal:0,2',
            'discount' => 'nullable|numeric|min:0|max:99999999.99|decimal:0,2',
            'tax' => 'nullable|numeric|min:0|max:99999999.99|decimal:0,2',
            'notes' => 'nullable|string|max:2000',
        ]);

        $commitAttempted = false;
        $transactionStarted = false;
        try {
            DB::beginTransaction();
            $transactionStarted = true;
            $menuItems = MenuItem::whereIn('id', collect($data['items'])->pluck('id')->unique())
                ->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $subtotal = 0;
            $lines = [];
            foreach ($data['items'] as $index => $item) {
                $menuItem = $menuItems->get($item['id']);
                if (! $menuItem || ! $menuItem->is_available) {
                    throw ValidationException::withMessages([
                        "items.$index.id" => 'An item is no longer available. Please update your order.',
                    ]);
                }
                // Calculate money in minor units, using the current database price.
                $price = (int) round((float) $menuItem->price * 100);
                $lineTotal = $price * $item['quantity'];
                $subtotal += $lineTotal;
                $lines[] = [
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $item['quantity'],
                    'price' => $price / 100,
                    'subtotal' => $lineTotal / 100,
                    'special_instructions' => $item['instructions'] ?? null,
                ];
            }
            $discount = (int) round((float) ($data['discount'] ?? 0) * 100);
            $tax = (int) round((float) ($data['tax'] ?? 0) * 100);
            if ($discount > $subtotal) {
                throw ValidationException::withMessages(['discount' => 'Discount cannot exceed the order subtotal.']);
            }
            $total = $subtotal + $tax - $discount;
            if ($subtotal > 9999999999 || $total > 9999999999) {
                throw ValidationException::withMessages(['items' => 'This order exceeds the supported receipt amount.']);
            }
            $cash = $data['payment_method'] === 'cash';
            $received = $cash ? (int) round((float) $data['cash_received'] * 100) : null;
            if ($cash && $received < $total) {
                throw ValidationException::withMessages(['cash_received' => 'Cash received must cover the total due.']);
            }
            $order = Order::create([
                'user_id' => $request->user()->id,
                'subtotal' => $subtotal / 100,
                'tax' => $tax / 100,
                'discount' => $discount / 100,
                'total' => $total / 100,
                'payment_method' => $data['payment_method'],
                'cash_received' => $cash ? $received / 100 : null,
                'change_amount' => $cash ? ($received - $total) / 100 : null,
                'notes' => $data['notes'] ?? null,
                'status' => 'completed',
                'completed_at' => now(),
            ]);
            $order->items()->createMany($lines);
            foreach ($data['items'] as $item) {
                $menuItem = $menuItems->get($item['id']);
                if ($menuItem->stock_id) {
                    $menuItem->stock()->decrement('quantity', $item['quantity']);
                }
            }
            $commitAttempted = true;
            DB::commit();

            return response()->json([
                'success' => true,
                'order' => $order->refresh(),
                'message' => 'Order created successfully!',
            ]);
        } catch (\Throwable $exception) {
            $rolledBack = false;
            if ($transactionStarted && DB::transactionLevel() > 0) {
                try {
                    DB::rollBack();
                    $rolledBack = true;
                } catch (\Throwable $rollbackException) {
                    report($rollbackException);
                }
            }
            if ($exception instanceof ValidationException) {
                throw $exception;
            }
            report($exception);

            return response()->json([
                'success' => false,
                // A failed commit/connection has an unknown outcome; never invite a blind retry.
                'order_saved' => ! $commitAttempted && ($rolledBack || ! $transactionStarted) ? false : null,
                'message' => ! $commitAttempted && ($rolledBack || ! $transactionStarted)
                    ? 'The order was not saved. Your items are still here; please try again.'
                    : 'The order status could not be confirmed. Check order history before trying again.',
            ], 500);
        }
    }

    public function invoice(Order $order)
    {
        if ($order->user_id !== auth()->id() && ! auth()->user()->isOwner() && ! auth()->user()->isManager()) {
            abort(403);
        }

        if (request()->has('print')) {
            return view('cashier.invoice', compact('order'))->with('print', true);
        }

        if (request()->has('view')) {
            return view('cashier.invoice', compact('order'));
        }

        $pdf = Pdf::loadView('cashier.invoice', compact('order'));

        if (request()->has('download')) {
            return $pdf->download('invoice-'.$order->invoice_number.'.pdf');
        }

        return $pdf->stream('invoice-'.$order->invoice_number.'.pdf', ['Attachment' => false]);
    }

    public function history()
    {
        $user = auth()->user();
        $query = Order::with(['items.menuItem', 'user']);

        if ($user->role === 'manager' || $user->role === 'owner') {
            // Managers and owners see all orders placed today by all entities
            $query->whereDate('created_at', today());
        } else {
            // Other roles (cashiers) see only their own history
            $query->where('user_id', $user->id);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('cashier.history', compact('orders'));
    }
}
