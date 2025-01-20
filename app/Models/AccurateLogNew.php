<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccurateLogNew extends Model
{
    use HasFactory;

    protected $table = 'accurate_log_new';
    public $timestamps = true;

    protected $fillable = [
        'kd_database',
        'scheduler',
        'rowCount',
        'updateRowCount',
        'startTime',
        'endTime',
        'duration',
        'updated_at'
    ];
}
