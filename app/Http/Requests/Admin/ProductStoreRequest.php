<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'sale_price' => 'nullable|numeric',
            'stock' => 'required|integer',

            'sku' => 'nullable|string',
            'description' => 'nullable|string',

            'is_active' => 'boolean',
            'is_featured' => 'boolean',

            'category_id' => 'required|exists:categories,id',
            'images.*' => 'image|max:2048',
        ];
    }
}
