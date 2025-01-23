<?php

namespace App\Repositories;

use App\Models\Denunciation;
use App\Repositories\Repository;

class Denunciations extends Repository
{
    public function __construct()
    {
        $this->model = Denunciation::query();
    }

    protected function filterByAlamat($query, $value)
    {
        $query->where('alamat', 'like', '%'.$value.'%');
    }

    protected function filterByUserPelaporId($query, $value)
    {
        $query->where('user_pelapor_id', $value);
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

    protected function filterByBulanTahun($query, $value)
    {
        $value = $carbonDate = Carbon::createFromFormat('d-m-Y', $value);
        $month = $carbonDate->month;
        $year = $carbonDate->year;

        $query->whereMonth('created_at', $month)
              ->whereYear('created_at', $year);
    }
}
