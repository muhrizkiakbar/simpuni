<?php

namespace App\Repositories;

use App\Models\ArchiveFile;
use App\Repositories\Repository;

class ArchiveFiles extends Repository
{
    public function __construct()
    {
        $this->model = ArchiveFile::query();
    }

    protected function filterByQ($query, $value)
    {
        $query->where('name', 'like', '%'.$value.'%');
    }

    protected function filterByType($query, $value)
    {
        $query->where('type', $value);
    }

    protected function filterByYear($query, $value)
    {
        $query->where('year', $value);
    }
}
