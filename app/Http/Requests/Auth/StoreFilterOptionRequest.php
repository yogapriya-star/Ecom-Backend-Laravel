<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class StoreFilterOptionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules()
    {
        return ['value' => 'required|string|unique:filter_options,value'];
    }

}