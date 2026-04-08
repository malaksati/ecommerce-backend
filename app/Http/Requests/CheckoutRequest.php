<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name'      => ['required', 'string', 'max:255'],
            'phone'          => ['required', 'string', 'max:20'],
            'street'         => ['required', 'string', 'max:255'],
            'city'           => ['required', 'string', 'max:255'],
            'country'        => ['required', 'string', 'max:255'],
            'postal_code'    => ['nullable', 'string', 'max:20'],
            'payment_method' => ['required', 'in:cash_on_delivery,credit_card,paypal'],
            'notes'          => ['nullable', 'string', 'max:500'],  // 👈 bonus
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.in' => 'Payment method must be cash_on_delivery, credit_card, or paypal',
        ];
    }
}
