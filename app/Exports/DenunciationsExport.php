<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DenunciationsExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $denunciations;

    public function __construct($denunciations)
    {
        $this->denunciations = $denunciations;
    }

    public function view(): View
    {
        return view('exports.denunciations', [
            'denunciations' => $this->denunciations
        ]);
    }
}
