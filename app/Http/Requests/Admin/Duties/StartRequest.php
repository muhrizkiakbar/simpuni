<?php

namespace App\Http\Requests\Admin\Duties;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StartRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_longitude' => [ 'required', 'string', 'max:255'  ],
            'start_latitude' => [ 'required', 'string', 'max:255'  ],
        ];
    }
}
