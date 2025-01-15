<?php

namespace App\Repositories;

use App\Models\FunctionBuilding;
use App\Repositories\Repository;

class FunctionBuildings extends Repository
{
    public function __construct()
    {
        $this->model = FunctionBuilding::query();
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


