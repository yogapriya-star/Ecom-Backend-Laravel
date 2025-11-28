<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'color' => 'nullable|string|max:100',
            'size' => 'nullable|string|max:100',
            'sku' => 'required|string|max:255|unique:product_variants,sku',
            'price_mrp' => 'required|numeric|min:0',
            'price_sp' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',

            // COMPOSITE UNIQUE VALIDATION
            // product_id is coming from body in your case
            'product_id' => [
                'required',
                'exists:products,id',
            ],

            // UNIQUE for (product_id, color, size)
            'color' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('product_variants')->where(function ($query) {
                    return $query
                        ->where('product_id', $this->product_id)
                        ->where('color', $this->color)
                        ->where('size', $this->size);
                }),
            ],
        ];
    }

}
