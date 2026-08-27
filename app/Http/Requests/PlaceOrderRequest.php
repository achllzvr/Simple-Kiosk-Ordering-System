<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'payment_method' => 'required|string',
            'agreement' => 'accepted',
            'mode' => 'required|in:dine-in,take-out,delivery',
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'required|string|max:40',
            'guest_email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'seating_option' => 'nullable|string',
        ];
    }
}
