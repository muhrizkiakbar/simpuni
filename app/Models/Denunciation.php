<?php

namespace App\Models;

use App\Models\User;
use App\Models\Duty;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Denunciation extends Model
{
    protected $table = 'denunciations';

    public function user_pelapor() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_pelapor_id');
    }

    public function type_denunciation() : BelongsTo
    {
        return $this->belongsTo(TypeDenunciation::class, 'type_denunciation_id');
    }

    public function duties() : HasMany
    {
        return $this->hasMany(Duty::class, 'denunciation_id', 'id');
    }
}
