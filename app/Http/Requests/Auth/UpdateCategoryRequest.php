<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('categories', 'name')
                    ->ignore($this->route('category')->id) 
            ],

            'parent_id' => [
                'nullable',
                'exists:categories,id'
            ],

            'slug' => [
                'nullable',
                Rule::unique('categories', 'slug')
                    ->ignore($this->route('category')->id),
            ],
            'position' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ];

    }

}
