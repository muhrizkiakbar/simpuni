<?php

namespace App\Outputs\Admin;

use App\Outputs\ApiOutput;

class LogDenunciationOutput extends ApiOutput
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
        $user_output = new UserOutput();

        $data = [
            'id' => $object->id,
            'user_admin'=> $user_output->renderJson($object->user_admin ?? [], "format", ["mode" => "raw_data"]),
            'current_state' => $object->current_state,
            'new_state' => $object->new_state,
            'slug' => encrypt($object->id),
        ];

        return $data;
    }
}

