<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BuildingsExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $buildings;

    public function __construct($buildings)
    {
        $this->buildings = $buildings;
    }

    public function view(): View
    {
        return view('exports.buildings', [
            'buildings' => $this->buildings
        ]);
    }
}
