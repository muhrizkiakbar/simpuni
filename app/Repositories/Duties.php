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

    protected function filterByDenunciation_Id($query, $value)
    {
        $query->where('denunciation_id', $value);
    }

    protected function filterByUser_Petugas_Id($query, $value)
    {
        $query->where('user_petugas_id', $value);
    }


    protected function filterByUser_Admin_Id($query, $value)
    {
        $query->where('user_admin_id', $value);
    }

    protected function filterByTanggal_Pengantaran($query, $value)
    {
        $query->whereDate('tanggal_pengantaran', $value);
    }

    protected function filterByTanggal_Penugasan($query, $value)
    {
        $query->whereDate('created_at', $value);
    }

    protected function filterByState_Type($query, $value)
    {
        $query->where('state_type', $value);
    }

}
