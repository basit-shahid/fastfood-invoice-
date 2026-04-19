<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $stocks = Stock::with('updater')->latest()->get();
        return view('stock.index', compact('stocks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
        ]);

        Stock::create([
            'item_name' => $request->item_name,
            'quantity' => $request->quantity,
            'unit' => $request->unit,
            'last_updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Stock item added successfully!');
    }

    public function update(Request $request, Stock $stock)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0',
        ]);

        $stock->update([
            'quantity' => $request->quantity,
            'last_updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Stock quantity updated!');
    }

    public function destroy(Stock $stock)
    {
        $stock->delete();
        return redirect()->back()->with('success', 'Stock item removed!');
    }
}
