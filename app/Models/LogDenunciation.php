<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Denunciation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogDenunciation extends Model
{
    protected $table = 'log_denunciations';
    protected $guarded = [];

    public function user_admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_admin_id');
    }

    public function denunciation(): BelongsTo
    {
        return $this->belongsTo(Denunciation::class, 'denunciation_id');
    }
}
