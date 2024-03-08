<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ContentService
{
    public static function getContent()
    {
        return DB::table('cms')->whereNull('deleted_at')->select('*')->first();
    }
}
