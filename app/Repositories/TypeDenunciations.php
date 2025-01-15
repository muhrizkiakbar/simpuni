<?php

namespace App\Repositories;

use App\Models\TypeDenunciation;
use App\Repositories\Repository;

class TypeDenunciations extends Repository
{
    public function __construct()
    {
        $this->model = TypeDenunciation::query();
    }

    protected function filterByQ($query, $value)
    {
        $query->where('name', 'like', '%'.$value.'%');
    }

    protected function filterByState($query, $value)
    {
        $query->where('state', $value);
    }

}


