<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class AssignProductFilterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules()
    {
        return [
            'filter_option_ids' => 'required|array',
            'filter_option_ids.*' => 'exists:filter_options,id',
        ];
    }
}
