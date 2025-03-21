<?php

namespace App\Outputs\Admin;

use App\Outputs\ApiOutput;
use App\Outputs\Admin\FunctionBuildingOutput;
use App\Outputs\Admin\UserOutput;
use App\Outputs\Admin\AttachmentOutput;
use Illuminate\Support\Facades\Storage;

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

        $data = [
            'id' => $object->id,
            'nomor_bangunan' => $object->nomor_bangunan,
            'nomor_izin_bangunan' => $object->nomor_izin_bangunan,
            'rw' => $object->rw,
            'rt' => $object->rt,
            'name' => $object->name,
            'alamat' => $object->alamat,
            'kecamatan_id' => $object->kecamatan_id,
            'kecamatan' => $object->kecamatan,
            'kelurahan_id' => $object->kelurahan_id,
            'kelurahan' => $object->kelurahan,
            'luas_bangunan' => $object->luas_bangunan,
            'banyak_lantai' => $object->banyak_lantai,
            'ketinggian' => $object->ketinggian,
            'longitude' => $object->longitude,
            'latitude' => $object->latitude,
            'function_building' => $function_building_output->renderJson($object->function_building, "format", [ "mode" => "raw_data"]) ?? [],
            'updated_by_user' => $user_output->renderJson($object->updated_by_user ?? [], "format", [ "mode" => "raw_data"]) ?? [],
            'created_by_user' => $user_output->renderJson($object->created_by_user ?? [], "format", [ "mode" => "raw_data"]) ?? [],
            'state' => $object->state,
            'slug' => encrypt($object->id),
            'deleted_at' => $object->deleted_at,
            'foto' => $object->foto ? $this->convertUrlFormatStorage(Storage::disk('public')->url($object->foto)) : null,
            'dokumen' => $object->dokumen ? $this->convertUrlFormatStorage(Storage::disk('public')->url($object->dokumen)) : null,
            'created_at' => $object->created_at,
            'updated_at' => $object->updated_at,
        ];

        return $data;
    }
}
