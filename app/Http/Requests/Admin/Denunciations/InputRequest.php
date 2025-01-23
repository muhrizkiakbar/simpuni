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
            'alamat' => [ 'required', 'string' ],
            'kecamatan_id' => [ 'required', 'string', 'max:15'  ],
            'kecamatan' => [ 'required', 'string', 'max:255'  ],
            'kelurahan_id' => [ 'required', 'string', 'max:15'  ],
            'kelurahan' => [ 'required', 'string', 'max:255'  ],
            'longitude' => [ 'required', 'string', 'max:255'  ],
            'latitude' => [ 'required', 'string', 'max:255'  ],
            'catatan' => [ 'required', 'string', 'max:255'  ],
            'attachments' => 'nullable|array|max:3',
            'attachments.*' => 'nullable|file|mimetypes:image/jpeg,image/png,image/jpg|max:512',
            'delete_attachment_ids' => 'nullable|array|'
        ];
    }
}

