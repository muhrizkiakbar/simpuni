<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        'file_name',
        'file_path',
        'mime_type',
        'size',
    ];

    /**
     * Get the parent attachable model (polymorphic relationship).
     */
    public function attachable()
    {
        return $this->morphTo();
    }
}
