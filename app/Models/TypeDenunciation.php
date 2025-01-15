<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeDenunciation extends Model
{
    //
    protected $table = 'type_denunciations';

    protected $attributes = [
        'state' => 'active',
    ];

    public function denunciations() : HasMany
    {
        return $this->hasMany(Denunciation::class, 'type_denunciation_id', 'id');
    }
}
