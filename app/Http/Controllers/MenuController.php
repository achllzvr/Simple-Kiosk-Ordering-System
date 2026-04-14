<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    public function index()
    {
        $items = MenuItem::orderBy('category')->orderBy('name')->get();
        
        return view('admin.menu.index', [
            'items' => $items,
        ]);
    }

    public function create()
    {
        $categories = MenuItem::distinct()->pluck('category')->filter(fn($cat) => !empty($cat))->toArray();
        
        return view('admin.menu.create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:menu_items,name'],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0.01', 'max:999.99'],
            'image' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        MenuItem::create($data);

        return redirect()->route('admin.menu.index')->with('success', 'Menu item created successfully.');
    }

    public function edit(MenuItem $item)
    {
        $categories = MenuItem::distinct()->pluck('category')->filter(fn($cat) => !empty($cat))->toArray();
        if (!in_array($item->category, $categories)) {
            $categories[] = $item->category;
        }
        
        return view('admin.menu.edit', [
            'item' => $item,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, MenuItem $item)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('menu_items', 'name')->ignore($item->id)],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0.01', 'max:999.99'],
            'image' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $item->update($data);

        return redirect()->route('admin.menu.index')->with('success', 'Menu item updated successfully.');
    }

    public function destroy(MenuItem $item)
    {
        $item->delete();

        return redirect()->route('admin.menu.index')->with('success', 'Menu item deleted successfully.');
    }
}
