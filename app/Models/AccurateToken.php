<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccurateToken extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'access_token',
        'token_type',
        'refresh_token',
        'expires_in',
        'deleted_at'
    ];

}
