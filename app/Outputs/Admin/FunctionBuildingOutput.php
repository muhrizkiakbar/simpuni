<?php

namespace App\Outputs\Admin;

use App\Outputs\ApiOutput;

class FunctionBuildingOutput extends ApiOutput
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
            'name' => $object->name,
            'state' => $object->state,
            'encrypt_id' => encrypt($object->id),
            'deleted_at' => $object->deleted_at,
        ];

        return $data;
    }
}

