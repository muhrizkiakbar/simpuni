<?php

namespace App\Http\Requests\Admin\Duties;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SubmitRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'catatan' => [ 'nullable', 'string', 'max:255'  ],
            'nomor_izin_bangunan' => [ 'nullable', 'string', 'max:255'  ],
            'foto' => 'nullable|file|mimetypes:image/jpeg,image/png,image/jpg|max:2048',
        ];
    }
}
