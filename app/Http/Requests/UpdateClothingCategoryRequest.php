<?php

namespace App\Http\Requests;

use App\Models\ClothingCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClothingCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var ClothingCategory $category */
        $category = $this->route('clothing_category');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('clothing_categories', 'name')->ignore($category),
            ],
        ];
    }
}
