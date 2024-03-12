<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mapping extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'kd_motor',
        'id_motor',
        'kd_produk',
        'deleted_at'
    ];
}
