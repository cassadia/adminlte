<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'kd_motor',
        'nm_motor',
        'tahun_dari',
        'tahun_sampai',
        'no_seri_mesin',
        'no_seri_rangka',
        'status',
        'gambar'
    ];
}
