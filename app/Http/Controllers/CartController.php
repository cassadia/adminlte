<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use App\Services\UserRoleService;
use App\Services\ContentService;
use App\Models\Transaction;

class CartController extends Controller
{
    protected $userRoleService;

    public  function __construct(UserRoleService $userRoleService)
    {
        $this->userRoleService = $userRoleService;
    }

    public function index(Request $request)
    {
        $emailUser = auth()->user()->email;
        $menusdua = $this->userRoleService->getUserRole($emailUser);
        $content = ContentService::getContent();

        return view('cart.index', compact('content', 'menusdua'));
    }

    // public function index(Request $request)
    // {
    //     // Mendapatkan email pengguna yang sedang login
    //     $emailUser = auth()->user()->email;

    //     // Ambil transaksi yang belum dihapus
    //     $transaction = Transaction::whereNull('deleted_at')->get();

    //     // Ambil data role pengguna
    //     $menusdua = $this->userRoleService->getUserRole($emailUser);

    //     // Ambil konten tambahan jika diperlukan
    //     $content = ContentService::getContent();

    //     // Kembalikan data dalam format JSON
    //     return response()->json([
    //         'status' => 'success',
    //         'data' => [
    //             'transactions' => $transaction,
    //             'userRoles' => $menusdua,
    //             'content' => $content,
    //         ],
    //     ], 200);
    // }    
}
