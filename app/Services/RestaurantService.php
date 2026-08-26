<?php

namespace App\Services;

use App\Models\Restaurant;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class RestaurantService
{
    public function listAll(): Collection
    {
        return Restaurant::query()->orderBy('name')->get();
    }

    public function listActive(): Collection
    {
        return Restaurant::query()->where('is_active', true)->orderBy('name')->get();
    }

    /**
     * Active stores sorted by distance from customer pin (km).
     */
    public function activeNearby(float $lat, float $lng, int $limit = 20): Collection
    {
        return $this->listActive()
            ->map(function (Restaurant $store) use ($lat, $lng) {
                $store->distance_km = $this->haversineKm($lat, $lng, (float) $store->lat, (float) $store->lng);

                return $store;
            })
            ->sortBy('distance_km')
            ->take($limit)
            ->values();
    }

    public function create(array $data): Restaurant
    {
        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        return Restaurant::create($data);
    }

    public function update(Restaurant $restaurant, array $data): Restaurant
    {
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $restaurant->update($data);

        return $restaurant->fresh();
    }

    public function delete(Restaurant $restaurant): void
    {
        if ($restaurant->orders()->exists()) {
            $restaurant->update(['is_active' => false]);
            $restaurant->delete();

            return;
        }

        $restaurant->forceDelete();
    }

    public function validationRules(?int $ignoreId = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('restaurants', 'name')->ignore($ignoreId)],
            'address' => ['nullable', 'string', 'max:255'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    private function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return round($earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a)), 2);
    }
}
