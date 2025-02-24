<?php

namespace App\Outputs\Admin;

use Illuminate\Support\Facades\Storage;
use App\Outputs\ApiOutput;

class ArchiveFileOutput extends ApiOutput
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
        $function_building_output = new FunctionBuildingOutput();
        $user_output = new UserOutput();

        $data = [
            'id' => $object->id,
            'name' => $object->name,
            'type' => $object->type,
            'year' => $object->year,
            'description' => $object->description,
            'attachment' => $object->attachment ? $this->convertUrlFormatStorage(Storage::disk('public')->url($object->attachment)) : null,
            'slug' => encrypt($object->id),
        ];

        return $data;
    }
}
