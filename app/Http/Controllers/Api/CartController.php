<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\UserRoleService;
use App\Services\ContentService;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class CartController extends Controller
{
    protected $userRoleService;

    public function __construct(UserRoleService $userRoleService)
    {
        $this->userRoleService = $userRoleService;
    }

    public function index(Request $request)
    {
        $toko = DB::table('accurate_db')
            ->where('deleted_at', null)
            ->get();

        // Siapkan array untuk hasil akhir
        $result = [];

        foreach ($toko as $value) {
            // Ambil transaksi berdasarkan 'kd_database' dari toko
            $transactions = Transaction::Join('products as p', function ($join) {
                $join->on('transaction.kd_produk', '=', 'p.kd_produk')
                    ->on('transaction.kd_database', '=', 'p.database');
            })
            ->select('transaction.*', 'p.nm_produk')
            ->where('transaction.kd_database', $value->kd_database)
            ->whereNull('transaction.deleted_at')
            ->where('is_send_to_accu', '!=', 1)
            ->where('kd_user', $request->kdUser)
            ->get();

            // Hanya tambahkan ke hasil jika transaksi tidak kosong
            if (!$transactions->isEmpty()) {
                // Format data transaksi menjadi array
                $transactionData = $transactions->map(function ($transaction) {
                    return [
                        'id' => $transaction->id ?? '',
                        'kdDB' => $transaction->kd_database ?? '',
                        'produk' => $transaction->kd_produk ?? '',
                        'nm_produk' => $transaction->nm_produk ?? '',
                        'qty' => $transaction->qty ?? '',
                        'harga' => $transaction->harga_jual ?? '',
                    ];
                });

                // Tambahkan data ke hasil akhir dengan key 'nm_database' sebagai nama toko
                $result[$value->nm_database] = $transactionData;
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $result,
        ], 200)->header('Cache-Control', 'no-store');
    }

    public function deleteCartByID(Request $request)
    {
        $cart = $request->input('cartId');
        $kdDB = $request->input('kdDB');
        $produk = $request->input('produk');
        $qty = $request->input('qty');
        $kdUser = $request->input('kdUser');

        $stock = Product::where('database', $kdDB)
            ->where('kd_produk', $produk)
            ->where('status', 'Aktif')
            ->whereNull('deleted_at')
            ->first();

        $returnStock = $stock->qty_available + $qty;

        Product::where('database', $kdDB)
            ->where('kd_produk', $produk)
            ->where('status', 'Aktif')
            ->whereNull('deleted_at')
            ->update([
                'qty_available' => $returnStock
            ]);

        $transaction = Transaction::where('id', $cart)
            ->where('kd_database', $kdDB)
            ->where('kd_produk', $produk)
            ->where('kd_user', $kdUser)
            ->whereNull('deleted_at')
            ->update([
                'deleted_at' => now()
            ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Item berhasil dihapus!'
        ], 200)->header('Cache-Control', 'no-store');
    }
}
