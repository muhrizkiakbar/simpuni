<?php

namespace App\Outputs\Admin;

use App\Outputs\Admin\DenunciationOutput;
use App\Outputs\ApiOutput;
use App\Outputs\Admin\UserOutput;

class DutyOutput extends ApiOutput
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
        $denunciation_output = new DenunciationOutput();
        $data = [
            'id' => $object->id,
            'state_type' => $object->state_type,
            'tanggal_pengiriman' => $object->tanggal_pengiriman,
            'catatan' => $object->catatan,
            'nomor_izin_bangunan' => $object->nomor_izin_bangunan,
            'user_pelapor' => $user_output->renderJson($object->user_pelapor ?? [], "format", [ "mode" => "raw_data"]) ?? [],
            'type_denunciation' => $type_denunciation_output->renderJson($object->type_denunciation ?? [], "format", [ "mode" => "raw_data" ]) ?? [],
            'function_building' => $function_building_output->renderJson($object->function_building ?? [], "format", [ "mode" => "raw_data"]) ?? [],
            'attachments' => $object->attachments->count() > 0 ? $attachment_output->renderJson($object->attachments, "format", ["mode" => "raw_many_data"]) : [],
            'state' => $object->state,
            'slug' => encrypt($object->id)
        ];

        return $data;
    }
}
