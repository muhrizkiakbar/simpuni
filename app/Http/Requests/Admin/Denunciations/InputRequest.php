<?php

namespace App\Http\Requests\Admin\Denunciations;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class InputRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'alamat' => [ 'nullable', 'filled', 'string' ],
            'kecamatan_id' => [ 'nullable', 'filled', 'string', 'max:15'  ],
            'kecamatan' => [ 'nullable', 'string', 'filled', 'max:255'  ],
            'kelurahan_id' => [ 'nullable', 'string', 'filled', 'max:15'  ],
            'kelurahan' => [ 'nullable', 'string', 'filled', 'max:255'  ],
            'longitude' => [ 'nullable', 'string', 'filled', 'max:255'  ],
            'latitude' => [ 'nullable', 'string', 'filled', 'max:255'  ],
            'catatan' => [ 'nullable', 'string', 'filled', 'max:255'  ],
            'attachments' => 'nullable|array|max:3',
            'attachments.*' => 'nullable|file|mimetypes:image/jpeg,image/png,image/jpg|max:512',
            'delete_attachment_ids' => 'nullable|array|filled'
        ];
    }
}
