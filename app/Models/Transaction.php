<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'transaction';

    protected $fillable = [
        'no_accu_trans',
        'kd_produk',
        'kd_produk_accu',
        'kd_motor',
        'harga_jual',
        'kd_database',
        'qty',
        'is_send_to_accu'
    ];
}
