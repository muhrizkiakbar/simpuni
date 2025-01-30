<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArchiveFile extends Model
{
    protected $table = 'archive_files';

    protected $guarded = [];
}
