<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class BuildingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nomor_izin_bangunan' => ['nullable','string'],
            'alamat' => [ 'nullable','string' ],
            'kecamatan_id' => [ 'nullable','string', 'max:15'  ],
            'kecamatan' => [ 'nullable','string', 'max:255'  ],
            'kelurahan_id' => [ 'nullable', 'string', 'max:15'  ],
            'kelurahan' => [ 'nullable', 'string', 'max:255'  ],
            'luas_bangunan' => [ 'nullable', 'integer' ],
            'banyak_lantai' => [ 'nullable', 'integer'  ],
            'ketinggian' => [ 'nullable', 'integer'  ],
            'longitude' => [ 'nullable', 'string', 'max:255'  ],
            'latitude' => [ 'nullable', 'string', 'max:255'  ],
            'nomor_bangunan' => [ 'nullable', 'string', 'max:255'  ],
            'rt' => [ 'nullable', 'string', 'max:255'  ],
            'rw' => [ 'nullable', 'string', 'max:255'  ],
            'function_building_id' => ['required', 'integer'],
            'foto' => 'nullable|file|mimetypes:image/jpeg,image/png,image/jpg|max:2048',
            'dokumen' => 'nullable|file|mimetypes:application/pdf|max:2048',
            'state' => [ 'nullable', 'string', 'max:255'  ],
        ];
        //'attachments' => 'nullable|array|max:3',
        //'attachments.*' => 'nullable|file|mimetypes:image/jpeg,image/png,image/jpg|max:512',
        //'attachments_id_deleted' => 'nullable|array|'
    }
}
