<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrowserInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_name', 'app_version', 'platform', 'user_agent', 'language', 'ip_address',
    ];
}
