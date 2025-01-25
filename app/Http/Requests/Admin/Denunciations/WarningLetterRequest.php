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
            'state' => [ 'nullable', 'filled', 'string' ],
            'user_petugas_id' => ['required', 'integer'],
            'surat_tugas' => 'nullable|file|mimetypes:application/pdf|max:2048',
            'foto' => 'nullable|file|mimetypes:image/jpeg,image/png,image/jpg|max:2048',
        ];
    }
}
