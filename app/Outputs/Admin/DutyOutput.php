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
            'user_petugas' => $user_output->renderJson($object->user_petugas ?? [], "format", [ "mode" => "raw_data"]) ?? [],
            'user_admin' => $user_output->renderJson($object->user_admin ?? [], "format", [ "mode" => "raw_data" ]) ?? [],
            'denunciation' => $denunciation_output->renderJson($object->denunciation ?? [], "format", [ "mode" => "raw_data"]) ?? [],
            'state' => $object->state,
            'foto' => $object->foto ? asset('storage/'.$object->foto) : null,
            'surat_tugas' => $object->surat_tugas ? asset('storage/'.$object->surat_tugas) : null,
            'slug' => encrypt($object->id)
        ];

        return $data;
    }


    public function mini_format($object, $options = [])
    {
        $user_output = new UserOutput();
        $denunciation_output = new DenunciationOutput();
        $data = [
            'id' => $object->id,
            'state_type' => $object->state_type,
            'tanggal_pengiriman' => $object->tanggal_pengiriman,
            'catatan' => $object->catatan,
            'nomor_izin_bangunan' => $object->nomor_izin_bangunan,
            'state' => $object->state,
            'foto' => $object->foto ? asset('storage/'.$object->foto) : null,
            'surat_tugas' => $object->surat_tugas ? asset('storage/'.$object->surat_tugas) : null,
            'slug' => encrypt($object->id)
        ];

        return $data;
    }
}
