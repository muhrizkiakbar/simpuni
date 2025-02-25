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
            'type_denunciation_id' => [ 'nullable',  'integer' ],
            'function_building_id' => [ 'nullable',  'integer' ],
            'description' => [ 'nullable', 'string' ],
            'warna_bangunan' => [ 'nullable', 'string' ],
            'pemilik_bangunan' => [ 'nullable', 'string' ],
            'jumlah_lantai' => [ 'nullable', 'integer' ],
            'material_utama' => [ 'nullable', 'string' ],
            'alamat' => [ 'nullable', 'string' ],
            'kecamatan_id' => [ 'nullable', 'string', 'max:15'  ],
            'kecamatan' => [ 'nullable', 'string', 'max:255'  ],
            'kelurahan_id' => [ 'nullable', 'string', 'max:15'  ],
            'kelurahan' => [ 'nullable', 'string', 'max:255'  ],
            'longitude' => [ 'nullable', 'string', 'max:255'  ],
            'latitude' => [ 'nullable', 'string', 'max:255'  ],
            'catatan' => [ 'nullable', 'string', 'max:255'  ],
            'catatan_reject' => [ 'nullable', 'string', 'max:255'  ],
            'attachments' => 'nullable|array|min:1|max:4',
            'attachments.*' => 'nullable|file|mimetypes:image/jpeg,image/png,image/jpg|max:2048',
            'delete_attachment_ids' => 'nullable|array'
        ];
    }
}
