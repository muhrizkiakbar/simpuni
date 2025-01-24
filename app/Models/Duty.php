<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Duty extends Model
{
    protected $table = 'duties';
    protected $guarded = [];

    public function denunciation(): BelongsTo
    {
        return $this->belongsTo(Denunciation::class, 'denunciation_id');
    }

    public function user_petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_petugas_id');
    }

    public function user_admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_admin_id');
    }

}
