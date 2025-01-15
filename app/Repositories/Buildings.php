<?php

namespace App\Repositories;

use App\Models\Building;
use App\Repositories\Repository;

class Buildings extends Repository
{
    public function __construct()
    {
        $this->model = Building::query();
    }

    protected function filterByTypeDenunciationId($query, $value)
    {
        $query->where('type_denunciation_id', $value);
    }

    protected function filterByFunctionBuildingId($query, $value)
    {
        $query->where('function_building_id', $value);
    }

    protected function filterByNomorBangunan($query, $value)
    {
        $query->where('nomor_bangunan', $value);
    }


    protected function filterByUserAdminId($query, $value)
    {
        $query->where('user_admin_id', $value);
    }

    protected function filterByUserSuperadminId($query, $value)
    {
        $query->where('user_superadmin_id', $value);
    }

    protected function filterByQ($query, $value)
    {
        $query->where('name','like', '%'.$value.'%');
    }

    protected function filterByKecamatanId($query, $value)
    {
        $query->where('kecamatan_id', $value);
    }

    protected function filterByKelurahanId($query, $value)
    {
        $query->where('kelurahan_id', $value);
    }

    protected function filterByTanggalPengaduan($query, $value)
    {
        $query->whereDate('tanggal_pengaduan', $value);
    }

    protected function filterByState($query, $value)
    {
        $query->where('state', $value);
    }

}


