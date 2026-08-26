<?php

namespace App\Services;

use App\Models\MenuItem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
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

    public function create(array $data, ?UploadedFile $imageFile = null): MenuItem
    {
        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        if ($imageFile) {
            $data['image'] = $imageFile->store('menu', 'public');
        }

        unset($data['image_file']);

        return MenuItem::create($data);
    }

    public function update(MenuItem $item, array $data, ?UploadedFile $imageFile = null): MenuItem
    {
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        if ($imageFile) {
            $this->deleteStoredImage($item->image);
            $data['image'] = $imageFile->store('menu', 'public');
        }

        unset($data['image_file']);
        $item->update($data);

        return $item->fresh();
    }

    public function delete(MenuItem $item): void
    {
        $item->delete();
    }

    public function validationRules(?int $ignoreId = null, bool $forUpload = false): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', Rule::unique('menu_items', 'name')->ignore($ignoreId)],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0.01', 'max:9999.99'],
            'is_active' => ['sometimes', 'boolean'],
        ];

        if ($forUpload) {
            $rules['image_file'] = ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:4096'];
        } else {
            $rules['image'] = ['nullable', 'string', 'max:500'];
        }

        return $rules;
    }

    private function deleteStoredImage(?string $path): void
    {
        if (! $path) {
            return;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'assets/')) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
