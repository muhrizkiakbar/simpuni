<?php

namespace App\Outputs\Admin;

use App\Outputs\Admin\DenunciationOutput;
use App\Outputs\ApiOutput;
use App\Outputs\Admin\UserOutput;
use Illuminate\Support\Facades\Storage;

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
        $attachment_output = new AttachmentOutput();
        $data = [
            'id' => $object->id,
            'state_type' => $object->state_type,
            'tanggal_pengantaran' => $object->tanggal_pengantaran,
            'catatan' => $object->catatan,
            'nomor_izin_bangunan' => $object->nomor_izin_bangunan,
            'start_longitude' => $object->start_longitude,
            'start_latitude' => $object->start_latitude,
            'submit_longitude' => $object->submit_longitude,
            'submit_latitude' => $object->submit_latitude,
            'user_petugas' => $user_output->renderJson($object->user_petugas ?? [], "format", [ "mode" => "raw_data"]) ?? [],
            'user_admin' => $user_output->renderJson($object->user_admin ?? [], "format", [ "mode" => "raw_data" ]) ?? [],
            'denunciation' => $denunciation_output->renderJson($object->denunciation ?? [], "format", [ "mode" => "raw_data"]) ?? [],
            'state' => $object->state,
            'attachments' => $object->attachments->count() > 0 ? $attachment_output->renderJson($object->attachments, "format", ["mode" => "raw_many_data"]) : [],
            'surat_tugas' => $object->surat_tugas ? asset('storage/'.$object->surat_tugas) : null,
            'slug' => encrypt($object->id)
        ];

        return $data;
    }


    public function mini_format($object, $options = [])
    {
        $attachment_output = new AttachmentOutput();
        $user_output = new UserOutput();
        $denunciation_output = new DenunciationOutput();
        $data = [
            'id' => $object->id,
            'state_type' => $object->state_type,
            'tanggal_pengantaran' => $object->tanggal_pengantaran,
            'catatan' => $object->catatan,
            'nomor_izin_bangunan' => $object->nomor_izin_bangunan,
            'start_longitude' => $object->start_longitude,
            'start_latitude' => $object->start_latitude,
            'submit_longitude' => $object->submit_longitude,
            'submit_latitude' => $object->submit_latitude,
            'state' => $object->state,
            'foto' => $object->foto ? Storage::disk('public')->url($object->foto) : null,
            'surat_tugas' => $object->surat_tugas ? Storage::disk('public')->url($object->surat_tugas) : null,
            'slug' => encrypt($object->id),
            'user_petugas' => $user_output->renderJson($object->user_petugas ?? [], "format", [ "mode" => "raw_data"]) ?? [],
            'user_admin' => $user_output->renderJson($object->user_admin ?? [], "format", [ "mode" => "raw_data" ]) ?? [],
            'attachments' => $object->attachments->count() > 0 ? $attachment_output->renderJson($object->attachments, "format", ["mode" => "raw_many_data"]) : [],
        ];

        return $data;
    }
}
