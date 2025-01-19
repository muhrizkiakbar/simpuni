<?php

namespace App\Http\Requests\Admin\Denunciations;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class WarningLetterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'alamat' => [ 'required', 'string' ],
            'catatan' => [ 'nullable', 'string', 'max:255'  ],
        ];
    }
}


