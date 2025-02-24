<?php

namespace App\Outputs\Admin;

use App\Outputs\ApiOutput;
use Illuminate\Support\Facades\Storage;

use function PHPUnit\Framework\isEmpty;

class AttachmentOutput extends ApiOutput
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
            'path' => asset('storage/'.$object->file_path),
        ];

        return $data;
    }
}
