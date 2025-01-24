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
            'nomor_izin_bangunan' => ['nullable','string', 'filled'],
            'alamat' => [ 'nullable','string', 'filled' ],
            'kecamatan_id' => [ 'nullable','string', 'filled', 'max:15'  ],
            'kecamatan' => [ 'nullable','string', 'filled', 'max:255'  ],
            'kelurahan_id' => [ 'nullable', 'string', 'filled', 'max:15'  ],
            'kelurahan' => [ 'nullable', 'string', 'filled', 'max:255'  ],
            'luas_bangunan' => [ 'nullable', 'integer', 'filled' ],
            'banyak_lantai' => [ 'required', 'integer', 'filled'  ],
            'ketinggian' => [ 'nullable', 'integer', 'filled'  ],
            'longitude' => [ 'nullable', 'string', 'filled', 'max:255'  ],
            'latitude' => [ 'nullable', 'string', 'filled', 'max:255'  ],
            'nomor_bangunan' => [ 'nullable', 'string', 'filled', 'max:255'  ],
            'rt' => [ 'required', 'string', 'filled', 'max:255'  ],
            'rw' => [ 'required', 'string', 'filled', 'max:255'  ],
            'function_building_id' => ['required', 'filled', 'integer'],
            'foto' => 'required|file|mimetypes:image/jpeg,image/png,image/jpg|max:2048',
            'dokumen' => 'nullable|file|mimetypes:application/pdf|max:2048',
            'state' => [ 'nullable', 'string', 'filled', 'max:255'  ],
        ];
        //'attachments' => 'nullable|array|max:3',
        //'attachments.*' => 'nullable|file|mimetypes:image/jpeg,image/png,image/jpg|max:512',
        //'attachments_id_deleted' => 'nullable|array|'
    }
}
