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


    protected function filterByFunction_Building_Id($query, $value)
    {
        $query->where('function_building_id', $value);
    }

    protected function filterByNomor_Bangunan($query, $value)
    {
        $query->where('nomor_bangunan', $value);
    }


    protected function filterByUpdated_By_User_Id($query, $value)
    {
        $query->where('updated_by_user_id', $value);
    }

    protected function filterByCreated_By_User_Id($query, $value)
    {
        $query->where('created_by_user_id', $value);
    }

    protected function filterByQ($query, $value)
    {
        $query->where('name', 'like', '%'.$value.'%');
    }

    protected function filterByKecamatan_Id($query, $value)
    {
        $query->where('kecamatan_id', $value);
    }

    protected function filterByKelurahan_Id($query, $value)
    {
        $query->where('kelurahan_id', $value);
    }

    protected function filterByTanggal_Pengaduan($query, $value)
    {
        $query->whereDate('tanggal_pengaduan', $value);
    }

    protected function filterByState($query, $value)
    {
        $query->where('state', $value);
    }

    protected function filterByBulan_Tahun($query, $value)
    {
        $value = $carbonDate = Carbon::createFromFormat('d-m-Y', $value);
        $month = $carbonDate->month;
        $year = $carbonDate->year;

        $query->whereMonth('created_at', $month)
              ->whereYear('created_at', $year);
    }
}
