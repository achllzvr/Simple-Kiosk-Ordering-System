<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'guest_name',
        'guest_phone',
        'guest_email',
        'tracking_token',
        'restaurant_id',
        'status',
        'payment_status',
        'payment_method',
        'total_price',
        'order_mode',
        'customer_lat',
        'customer_lng',
        'notes',
        'paymongo_checkout_session_id',
        'paymongo_payment_id',
        'external_payment_ref',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'customer_lat' => 'float',
            'customer_lng' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }
}
