<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccuratePage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'accurate_page';
    public $timestamps = true;

    protected $fillable = [
        'batch',
        'startPage',
        'endPage',
        'totalBatches',
        'rowCount',
        'deleted_at',
        'kd_database',
        'updateRowCount',
        'updated_at'
    ];
}
