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
        return $this->user()->isAdmin();
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
            'price'      => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'required|integer|min:0',

            'sku' => 'nullable|string|unique:products,sku,' . $this->route('product'),
            'description' => 'nullable|string',

            'is_active' => 'boolean',
            'is_featured' => 'boolean',

            'category_id' => 'required|exists:categories,id',
            'images'   => 'nullable|array|max:5',       // max 5 images
            'images.*' => 'image|mimes:jpeg,png,webp|max:2048',
        ];
    }
    public function messages(): array
    {
        return [
            'category_id.exists'  => 'Selected category does not exist',
            'sale_price.lt'       => 'Sale price must be less than the regular price',
            'images.max'          => 'You can upload a maximum of 5 images',
            'images.*.mimes'      => 'Images must be jpeg, png, or webp',
            'images.*.max'        => 'Each image must not exceed 2MB',
            'sku.unique'          => 'This SKU is already used by another product',
        ];
    }
}
