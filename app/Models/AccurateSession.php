<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccurateSession extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'host',
        'session',
        'admin',
        'data_version',
        'accessible_until',
        'license_end',
        'deleted_at',
        'kd_database'
    ];
}
