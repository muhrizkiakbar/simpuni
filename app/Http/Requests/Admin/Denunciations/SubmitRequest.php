<?php

namespace App\Http\Requests\Admin\Denunciations;

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
            'type_denunciation_id' => [ 'nullable', 'filled', 'integer' ],
            'function_building_id' => [ 'nullable', 'filled', 'integer' ],
            'description' => [ 'nullable', 'string', 'filled' ],
            'description' => [ 'nullable', 'string', 'filled' ],
            'warna_bangunan' => [ 'nullable', 'string', 'filled' ],
            'jumlah_lantai' => [ 'nullable', 'integer', 'filled' ],
            'material_utama' => [ 'nullable', 'string', 'filled' ],
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
