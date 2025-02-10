<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserRoleService;
use App\Services\ContentService;
use App\Models\User;

class ProfileController extends Controller
{
    protected $userRoleService;

    public function __construct(UserRoleService $userRoleService)
    {
        $this->userRoleService = $userRoleService;
    }

    public function getUserByEmail(Request $request)
    {
        $emailUser = auth()->user()->email;
        $dataUser = User::whereNull('deleted_at')
            ->where('email', $emailUser)
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $dataUser
            ],
        ], 200);
    }
}
