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

    protected function filterByAlamat($query,$value) {
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

}


