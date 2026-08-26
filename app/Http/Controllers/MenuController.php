<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Services\MenuService;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function __construct(private MenuService $menuService) {}

    public function index()
    {
        return view('admin.menu.index', [
            'items' => $this->menuService->listAll(),
        ]);
    }

    public function create()
    {
        return view('admin.menu.create', [
            'categories' => $this->menuService->categories(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->menuService->validationRules());
        $data['is_active'] = $request->boolean('is_active', true);
        $this->menuService->create($data);

        return redirect()->route('admin.menu.index')->with('success', 'Menu item created successfully.');
    }

    public function edit(MenuItem $item)
    {
        $categories = $this->menuService->categories();
        if (! in_array($item->category, $categories, true)) {
            $categories[] = $item->category;
        }

        return view('admin.menu.edit', [
            'item' => $item,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, MenuItem $item)
    {
        $data = $request->validate($this->menuService->validationRules($item->id));
        $data['is_active'] = $request->boolean('is_active');
        $this->menuService->update($item, $data);

        return redirect()->route('admin.menu.index')->with('success', 'Menu item updated successfully.');
    }

    public function destroy(MenuItem $item)
    {
        $this->menuService->delete($item);

        return redirect()->route('admin.menu.index')->with('success', 'Menu item deleted successfully.');
    }
}
