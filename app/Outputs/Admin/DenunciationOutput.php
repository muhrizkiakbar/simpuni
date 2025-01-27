<?php

namespace App\Outputs\Admin;

use App\Outputs\Admin\TypeDenunciationOutput;
use App\Outputs\ApiOutput;
use App\Outputs\Admin\UserOutput;
use App\Outputs\Admin\FunctionBuildingOutput;

class DenunciationOutput extends ApiOutput
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
        $type_denunciation_output = new TypeDenunciationOutput();
        $function_building_output = new FunctionBuildingOutput();
        $attachment_output = new AttachmentOutput();
        $data = [
            'id' => $object->id,
            'alamat' => $object->alamat,
            'kecamatan_id' => $object->kecamatan_id,
            'kecamatan' => $object->kecamatan,
            'kelurahan_id' => $object->keluarahan_id,
            'kelurahan' => $object->kelurahan,
            'longitude' => $object->longitude,
            'latitude' => $object->latitude,
            'catatan' => $object->catatan,
            'description' => $object->description,
            'warna_bangunan' => $object->warna_bangunan,
            'jumlah_lantai' => $object->jumlah_lantai,
            'material_utama' => $object->material_utama,
            'user_pelapor' => $user_output->renderJson($object->user_pelapor ?? [], "format", [ "mode" => "raw_data"]) ?? [],
            'type_denunciation' => $type_denunciation_output->renderJson($object->type_denunciation ?? [], "format", [ "mode" => "raw_data" ]) ?? [],
            'function_building' => $function_building_output->renderJson($object->function_building ?? [], "format", [ "mode" => "raw_data"]) ?? [],
            'attachments' => $object->attachments->count() > 0 ? $attachment_output->renderJson($object->attachments, "format", ["mode" => "raw_many_data"]) : [],
            'state' => $object->state,
            'slug' => encrypt($object->id)
        ];

        return $data;
    }

    public function detail_format($object, $options = [])
    {
        $user_output = new UserOutput();
        $type_denunciation_output = new TypeDenunciationOutput();
        $attachment_output = new AttachmentOutput();
        $function_building_output = new FunctionBuildingOutput();
        $log_output = new LogDenunciationOutput();

        $data = [
            'id' => $object->id,
            'alamat' => $object->alamat,
            'kecamatan_id' => $object->kecamatan_id,
            'kecamatan' => $object->kecamatan,
            'kelurahan_id' => $object->keluarahan_id,
            'kelurahan' => $object->kelurahan,
            'longitude' => $object->longitude,
            'latitude' => $object->latitude,
            'catatan' => $object->catatan,
            'user_pelapor' => $user_output->renderJson($object->user_pelapor ?? [], "format", [ "mode" => "raw_data"]) ?? [],
            'type_denunciation' => $type_denunciation_output->renderJson($object->type_denunciation ?? [], "format", [ "mode" => "raw_data" ]) ?? [],
            'function_building' => $function_building_output->renderJson($object->function_building ?? [], "format", [ "mode" => "raw_data"]) ?? [],
            'attachments' => $object->attachments->count() > 0 ? $attachment_output->renderJson($object->attachments, "format", ["mode" => "raw_many_data"]) : [],
            'logs' => $object->log_denunciations->count() > 0 ? $log_output->renderJson($object->log_denunciations, "format", ["mode" => "raw_many_data"]) : [],
            'slug' => encrypt($object->id),
            'state' => $object->state,
        ];

        return $data;
    }
}
