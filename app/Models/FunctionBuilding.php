<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FunctionBuilding extends Model
{
    //
    protected $table = 'function_buildings';
    protected $guarded = [];

    protected $attributes = [
        'state' => 'active',
    ];
}
