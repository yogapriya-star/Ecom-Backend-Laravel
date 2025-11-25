<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'nullable|string|max:255',
            'email'          => 'nullable|email',
            'phone'          => 'nullable|string|max:20',
            'address_line1'  => 'required|string|max:255',
            'address_line2'  => 'nullable|string|max:255',
            'city'           => 'required|string|max:100',
            'state'          => 'required|string|max:100',
            'zipcode'        => 'required|string|max:20',
            'order_notes'    => 'nullable|string',
        ];
    }
}
