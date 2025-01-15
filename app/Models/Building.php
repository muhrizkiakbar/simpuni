<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Repositories\FunctionBuildings;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Building extends Model
{
    protected $table = 'buildings';

    protected $attributes = [
        'state' => 'active',
    ];

    public function user_admin() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_admin_id');
    }

    public function user_superadmin() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_superadmin_id');
    }

    public function function_building() : BelongsTo
    {
        return $this->belongsTo(FunctionBuilding::class, 'function_building_id');
    }
}
