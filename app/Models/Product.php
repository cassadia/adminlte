<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'kd_produk',
        'kd_produk_accu',
        'nm_produk',
        'harga_jual',
        'qty_available',
        'database',
        'status'
    ];
}
