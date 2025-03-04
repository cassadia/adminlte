<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class UserRoleService
{
    public function getUserRole($userid)
    {
        return DB::table('user_assign as a')
            ->select('c.menu', 'c.menu_link', 'c.menu_deskripsi', 'c.menu_icon', 'b.has_public_path')
            ->join('users as b', 'b.id', '=', 'a.kd_user')
            ->join('user_menu as c', 'c.id', '=', 'a.id_user_permission')
            ->where('b.email', $userid)
            ->where('b.status', 'Aktif')
            ->get();
    }
}
