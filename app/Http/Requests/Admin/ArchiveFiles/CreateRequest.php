<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ArchiveFileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required','string'],
            'year' => [ 'nullable','string' ],
            'type' => [ 'nullable','string' ],
            'description' => [ 'nullable','string' ],
            'attachment' => 'required|file|mimetypes:image/jpeg,image/png,image/jpg,application/pdf,application/xlsx|max:3072',
        ];
    }
}
