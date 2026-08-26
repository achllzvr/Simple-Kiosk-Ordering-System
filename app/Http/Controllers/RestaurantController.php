<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Services\RestaurantService;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function __construct(private RestaurantService $restaurantService) {}

    public function index()
    {
        return view('admin.restaurants.index', [
            'restaurants' => $this->restaurantService->listAll(),
        ]);
    }

    public function create()
    {
        return view('admin.restaurants.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->restaurantService->validationRules());
        $data['is_active'] = $request->boolean('is_active', true);
        $this->restaurantService->create($data);

        return redirect()->route('admin.restaurants.index')->with('success', 'Store created successfully.');
    }

    public function edit(Restaurant $restaurant)
    {
        return view('admin.restaurants.edit', [
            'restaurant' => $restaurant,
        ]);
    }

    public function update(Request $request, Restaurant $restaurant)
    {
        $data = $request->validate($this->restaurantService->validationRules($restaurant->id));
        $data['is_active'] = $request->boolean('is_active');
        $this->restaurantService->update($restaurant, $data);

        return redirect()->route('admin.restaurants.index')->with('success', 'Store updated successfully.');
    }

    public function destroy(Restaurant $restaurant)
    {
        $this->restaurantService->delete($restaurant);

        return redirect()->route('admin.restaurants.index')->with('success', 'Store removed successfully.');
    }
}
