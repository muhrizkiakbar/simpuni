<?php

namespace App\Models;

use App\Models\User;
use App\Models\TypeDenunciation;
use App\Models\FunctionBuilding;
use App\Models\Duty;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Denunciation extends Model
{
    protected $table = 'denunciations';
    protected $guarded = [];

    protected $attributes = [
        'state' => 'sent',
    ];

    public function user_pelapor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_pelapor_id');
    }

    public function type_denunciation(): BelongsTo
    {
        return $this->belongsTo(TypeDenunciation::class, 'type_denunciation_id');
    }

    public function function_building(): BelongsTo
    {
        return $this->belongsTo(FunctionBuilding::class, 'function_building_id');
    }

    public function duties(): HasMany
    {
        return $this->hasMany(Duty::class, 'denunciation_id', 'id');
    }

    public function log_denunciations(): HasMany
    {
        return $this->hasMany(LogDenunciation::class, 'denunciation_id', 'id');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
