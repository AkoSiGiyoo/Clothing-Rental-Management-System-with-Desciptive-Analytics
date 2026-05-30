<?php

namespace App\Http\Requests;

use App\Models\ClothingItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClothingItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clothing_category_id' => ['required', 'exists:clothing_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'rental_price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
            'color' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'string', 'max:20'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'brand' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(ClothingItem::STATUSES)],
        ];
    }
}
