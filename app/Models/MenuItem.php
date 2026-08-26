<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class MenuItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'category', 'price', 'image', 'is_active'];

    protected $appends = ['image_url'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'menu_item_id');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class, 'menu_item_id');
    }

    /**
     * Public URL for menu image (upload, bundled asset, or remote URL).
     */
    public function getImageUrlAttribute(): string
    {
        $image = $this->image;

        if (! $image) {
            return asset('assets/images/KFC_logo_full_icon.png');
        }

        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        if (str_starts_with($image, 'assets/')) {
            return asset($image);
        }

        return Storage::disk('public')->url($image);
    }
}
