<?php
// app/Http/Controllers/OrderController.php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,card,online',
            'cash_received' => 'required_if:payment_method,cash|nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => auth()->id(),
                'subtotal' => 0,
                'tax' => $request->tax ?? 0,
                'discount' => $request->discount ?? 0,
                'total' => 0,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'status' => 'pending',
            ]);

            $subtotal = 0;

            foreach ($request->items as $item) {
                $menuItem = MenuItem::find($item['id']);
                $itemSubtotal = $menuItem->price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $order->items()->create([
                    'menu_item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $menuItem->price,
                    'subtotal' => $itemSubtotal,
                    'special_instructions' => $item['instructions'] ?? null,
                ]);
            }

            $total = $subtotal + ($request->tax ?? 0) - ($request->discount ?? 0);
            
            $order->update([
                'subtotal' => $subtotal,
                'total' => $total,
                'cash_received' => $request->cash_received,
                'change_amount' => $request->cash_received ? $request->cash_received - $total : null,
                'status' => 'completed',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'order' => $order,
                'message' => 'Order created successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function invoice(Order $order)
    {
        if ($order->user_id !== auth()->id() && !auth()->user()->isOwner() && !auth()->user()->isManager()) {
            abort(403);
        }

        if (request()->has('print')) {
            return view('cashier.invoice', compact('order'))->with('print', true);
        }

        if (request()->has('view')) {
            return view('cashier.invoice', compact('order'));
        }

        $pdf = PDF::loadView('cashier.invoice', compact('order'));
        
        if (request()->has('download')) {
            return $pdf->download('invoice-' . $order->invoice_number . '.pdf');
        }
        
        return $pdf->stream('invoice-' . $order->invoice_number . '.pdf', ['Attachment' => false]);
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