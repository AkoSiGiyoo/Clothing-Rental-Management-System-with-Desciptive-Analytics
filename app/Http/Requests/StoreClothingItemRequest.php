<?php

namespace App\Http\Requests;

use App\Models\ClothingItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClothingItemRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (! $this->filled('clothing_category_id') && $this->filled('category_id')) {
            $this->merge([
                'clothing_category_id' => $this->input('category_id'),
            ]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clothing_category_id' => ['required', Rule::exists('clothing_categories', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'rental_price' => ['required', 'numeric', 'min:0'],
            'color' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'string', 'max:20'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(ClothingItem::STATUSES)],
        ];
    }
}
