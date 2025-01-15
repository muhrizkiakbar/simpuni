<?php

namespace App\Repositories;

class Repository
{
    protected $model;

    public function filter(array $params, array $withRelationships = [], array $loadRelationships = [])
    {
        $query = $this->model;

        // Eager load the relationships at the beginning of the query using `with()`
        if (!empty($withRelationships)) {
            $query->with($withRelationships);
        }

        foreach ($params as $key => $value) {
            if (!empty($value) && method_exists($this, $method = 'filterBy' . ucfirst($key))) {
                $this->$method($query, $value);
            }
        }

        // If `loadRelationships` is provided, use `load()` to load the relationships after the query is executed
        if (!empty($loadRelationships)) {
            $query->get();  // Execute the query first
            $query->load($loadRelationships);  // Then load the relationships
        }

        return $query;
    }

}
