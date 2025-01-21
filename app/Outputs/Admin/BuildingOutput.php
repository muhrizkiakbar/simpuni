<?php

namespace App\Outputs\Admin;

use App\Outputs\ApiOutput;
use App\Outputs\Admin\FunctionBuildingOutput;
use App\Outputs\Admin\UserOutput;
use App\Outputs\Admin\AttachmentOutput;

class BuildingOutput extends ApiOutput
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
        $attachment_output = new AttachmentOutput();

        $data = [
            'id' => $object->id,
            'nomor_bangunan' => $object->nomor_bangunan,
            'nomor_izin_bangunan' => $object->nomor_izin_bangunan,
            'rw' => $object->rw,
            'rt' => $object->rt,
            'name' => $object->name,
            'alamat' => $object->alamat,
            'kecamatan_id'=> $object->kecamatan_id,
            'kecamatan' => $object->kecamatan,
            'kelurahan_id' => $object->keluarahan_id,
            'kelurahan' => $object->kelurahan,
            'luas_bangunan' => $object->luas_bangunan,
            'banyak_lantai' => $object->banyak_lantai,
            'ketinggian' => $object->ketinggian,
            'longitude' => $object->longitude,
            'latitude' => $object->latitude,
            'function_building'=> $function_building_output->renderJson($object->function_building, "format", [ "mode" => "raw_data"]) ?? [],
            'updated_by_user'=> $user_output->renderJson($object->updated_by_user ?? [], "format", [ "mode" => "raw_data"]) ?? [],
            'created_by_user'=> $user_output->renderJson($object->created_by_user ?? [], "format", [ "mode" => "raw_data"]) ?? [],
            'state' => $object->state,
            'slug' => encrypt($object->id),
            'deleted_at' => $object->deleted_at,
            'foto' => $object->foto ? asset('storage/'.$object->foto) : null,
            'dokumen' => $object->dokumen ? asset('storage/'.$object->dokumen) : null,
        ];

            //'attachments' => $object->attachments->count() > 0 ? $attachment_output->renderJson($object->attachments, "format", ["mode"=>"raw_many_data"]) : [],
        return $data;
    }
}

