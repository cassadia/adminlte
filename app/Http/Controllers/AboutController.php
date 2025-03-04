<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\UserRoleService;
use App\Services\ContentService;

class AboutController extends Controller
{
    protected $userRoleService;

    public function __construct(UserRoleService $userRoleService)
    {
        $this->middleware('auth');
        $this->userRoleService = $userRoleService;
    }

    public function index(Request $request)
    {
        $emailUser = auth()->user()->email;
        $menusdua = $this->userRoleService->getUserRole($emailUser);
        $content = ContentService::getContent();
        $publicPathDB = $menusdua->pluck('has_public_path')->unique();

        return view('about', compact('content', 'menusdua', 'publicPathDB'));
    }
}
