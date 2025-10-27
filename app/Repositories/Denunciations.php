<?php

namespace App\Repositories;

use Carbon\Carbon;
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

    protected function filterByUser_Pelapor_Id($query, $value)
    {
        $query->where('user_pelapor_id', $value);
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

    protected function filterByRequire_Action($query, $value)
    {
        if ($value) {
            $query->where('updated_at', '<', Carbon::now()->subDays(14))->whereNotIn('state', ['done', 'reject', 'teguran_lisan']);
            //$query->where('updated_at', '<', Carbon::now())->whereNotIn('state', ['done', 'reject']);
        }
    }
}
