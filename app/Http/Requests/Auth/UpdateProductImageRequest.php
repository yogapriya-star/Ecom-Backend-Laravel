<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => 'nullable|image|max:5120',
            'alt_text' => 'nullable|string|max:255',
            'position' => 'nullable|integer|min:1',
        ];
    }
}
