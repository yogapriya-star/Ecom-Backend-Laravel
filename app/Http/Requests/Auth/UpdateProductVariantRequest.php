<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->route('variant') ?? null;

        return [
            'product_id' => 'required|exists:products,id',

            'color' => [
                'required',
                'string',
                Rule::unique('product_variants')
                    ->where('product_id', $this->product_id)
                    ->where('size', $this->size)
                    ->ignore($id)
            ],

            'size' => [
                'required',
                'string',
                Rule::unique('product_variants')
                    ->where('product_id', $this->product_id)
                    ->where('color', $this->color)
                    ->ignore($id)
            ],

            'sku' => [
                'required',
                'string',
                Rule::unique('product_variants', 'sku')->ignore($id)
            ],

            'price_mrp' => 'required|numeric|min:0',
            'price_sp' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ];
    }
}
