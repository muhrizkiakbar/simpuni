<?php

namespace App\Outputs\Admin;

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
            'year' => $object->year,
            'description' => $object->description,
            'attachment' => $object->attachment ? asset('storage/'.$object->attachment) : null,
            'slug' => encrypt($object->id),
        ];

        return $data;
    }
}
