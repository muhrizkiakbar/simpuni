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

    public function updated_by_user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public function created_by_user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function function_building() : BelongsTo
    {
        return $this->belongsTo(FunctionBuilding::class, 'function_building_id');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
