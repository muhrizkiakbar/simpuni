<?php

namespace App\Http\Requests\Admin\ArchiveFiles;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'attachment' => 'nullable|file|mimetypes:image/jpeg,image/png,image/jpg,application/pdf,application/xlsx|max:3072',
        ];
    }
}
