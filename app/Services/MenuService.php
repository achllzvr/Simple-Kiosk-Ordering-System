<?php

namespace App\Services;

use App\Models\MenuItem;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class MenuService
{
    public function listActive(?string $search = null): Collection
    {
        $query = MenuItem::query()->where('is_active', true)->orderBy('category')->orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    public function listAll(): Collection
    {
        return MenuItem::query()->orderBy('category')->orderBy('name')->get();
    }

    public function categories(): array
    {
        return MenuItem::query()
            ->distinct()
            ->pluck('category')
            ->filter(fn ($cat) => ! empty($cat))
            ->values()
            ->all();
    }

    public function create(array $data): MenuItem
    {
        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        return MenuItem::create($data);
    }

    public function update(MenuItem $item, array $data): MenuItem
    {
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $item->update($data);

        return $item->fresh();
    }

    public function delete(MenuItem $item): void
    {
        $item->delete();
    }

    public function validationRules(?int $ignoreId = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('menu_items', 'name')->ignore($ignoreId)],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0.01', 'max:9999.99'],
            'image' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
