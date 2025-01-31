<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Repository;

class Users extends Repository
{
    public function __construct()
    {
        $this->model = User::query();
    }

    protected function filterByUsername($query, $value)
    {
        $query->where('username', $value);
    }

    protected function filterByQ($query, $value)
    {
        $query->where('name', 'like', '%'.$value.'%')
            ->orWhere('posisi', 'like', '%'.$value.'%')
            ->orWhere('instansi', 'like', '%'.$value.'%');
    }

    protected function filterByEmail($query, $value)
    {
        $query->where('email', $value);
    }

    protected function filterByType_User($query, $value)
    {
        $query->where('type_user', $value);
    }

    protected function filterByState($query, $value)
    {
        $query->where('state', $value);
    }

}
