<?php

namespace App\Repositories;

use App\Models\Duty;
use App\Repositories\Repository;

class Duties extends Repository
{
    public function __construct()
    {
        $this->model = Duty::query();
    }

    protected function filterByDenunciationId($query, $value)
    {
        $query->where('denunciation_id', $value);
    }

    protected function filterByUserPetugasId($query, $value)
    {
        $query->where('user_petugas_id', $value);
    }


    protected function filterByUserAdminId($query, $value)
    {
        $query->where('user_admin_id', $value);
    }

    protected function filterByTanggalPengantaran($query, $value)
    {
        $query->whereDate('tanggal_pengantaran', $value);
    }

    protected function filterByTanggalPenugasan($query, $value)
    {
        $query->whereDate('created_at', $value);
    }

    protected function filterByStateType($query, $value)
    {
        $query->where('state_type', $value);
    }

}


