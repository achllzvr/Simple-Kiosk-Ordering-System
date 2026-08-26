<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymongoWebhookLog extends Model
{
    protected $fillable = [
        'event_id',
        'event_type',
        'order_id',
        'signature_valid',
        'fulfilled',
        'payload_summary',
        'fulfill_message',
    ];

    protected function casts(): array
    {
        return [
            'signature_valid' => 'boolean',
            'fulfilled' => 'boolean',
        ];
    }
}
