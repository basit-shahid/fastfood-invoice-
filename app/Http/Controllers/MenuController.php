<?php
// app/Http/Controllers/MenuController.php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        $menuItems = MenuItem::orderBy('category')->orderBy('name')->get();
        $categories = MenuItem::distinct()->pluck('category');
        
        return view('menu.index', compact('menuItems', 'categories'));
    }

    public function create()
    {
        return view('menu.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
            'preparation_time' => 'integer|min:1',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menu-items', 'public');
            $validated['image'] = $path;
        }

        MenuItem::create($validated);

        return redirect()->route('menu.index')->with('success', 'Menu item added successfully!');
    }

    public function edit(MenuItem $menuItem)
    {
        return view('menu.form', compact('menuItem'));
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
            'preparation_time' => 'integer|min:1',
        ]);

        if ($request->hasFile('image')) {
            if ($menuItem->image) {
                Storage::disk('public')->delete($menuItem->image);
            }
            $path = $request->file('image')->store('menu-items', 'public');
            $validated['image'] = $path;
        }

        $menuItem->update($validated);

        return redirect()->route('menu.index')->with('success', 'Menu item updated successfully!');
    }

    public function destroy(MenuItem $menuItem)
    {
        if ($menuItem->image) {
            Storage::disk('public')->delete($menuItem->image);
        }
        
        $menuItem->delete();

        return redirect()->route('menu.index')->with('success', 'Menu item deleted successfully!');
    }

    public function toggleAvailability(MenuItem $menuItem)
    {
        $menuItem->update(['is_available' => !$menuItem->is_available]);
        
        return response()->json(['success' => true, 'is_available' => $menuItem->is_available]);
    }
}