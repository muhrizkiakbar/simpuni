<?php

namespace App\Outputs\Admin;

use App\Outputs\ApiOutput;
use Illuminate\Support\Facades\Storage;

class UserOutput extends ApiOutput
{
    /**
     * Format the data for a single object or collection.
     *
     * @param mixed $object
     * @param array $fields
     * @return array
     */
    public function format($object, $options = [])
    {
        $data = [
            'id' => $object->id,
            'avatar' => $object->avatar ? $this->convertUrlFormatStorage(Storage::disk('public')->url($object->avatar)) : null,
            'surat_tugas' => $object->surat_tugas ? $this->convertUrlFormatStorage(Storage::disk('public')->url($object->surat_tugas)) : null,
            'name' => $object->name,
            'posisi' => $object->posisi,
            'instansi' => $object->instansi,
            'type_user' => $object->type_user,
            'email' => $object->email,
            'username' => $object->username,
            'state' => $object->state,
            'slug' => encrypt($object->id),
            'deleted_at' => $object->deleted_at,
        ];

        return $data;
    }
}
