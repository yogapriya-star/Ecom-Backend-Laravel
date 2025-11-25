<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'email'         => 'required|email',
            'phone'         => 'required|string|max:20',
            'address_line1' => 'required|string|max:255|unique:addresses,address_line1',
            'address_line2' => 'required|string|max:255',
            'city'          => 'required|string|max:100',
            'state'         => 'required|string|max:100',
            'zipcode'       => 'required|string|max:10',
            'order_notes'   => 'nullable|string'
        ];
    }

    public function messages()
    {
        return [
            'address_line1.required' => 'The address line is required.',
            'address_line1.unique'   => 'This address line already exists.'
        ];
    }
}
