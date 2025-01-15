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
            'nomor_bangunan' => ['nullable','string'],
            'alamat' => [ 'required', 'string' ],
            'kecamatan_id' => [ 'required', 'string', 'max:15'  ],
            'kecamatan' => [ 'required', 'string', 'max:255'  ],
            'kelurahan_id' => [ 'required', 'string', 'max:15'  ],
            'kelurahan' => [ 'required', 'string', 'max:255'  ],
            'luas_bangunan' => [ 'required', 'integer' ],
            'banyak_lantai' => [ 'required', 'integer'  ],
            'ketinggian' => [ 'required', 'integer'  ],
            'longitude' => [ 'required', 'string', 'max:255'  ],
            'latitude' => [ 'required', 'string', 'max:255'  ],
            'function_building_id' => ['required', 'integer']
        ];
    }
}

