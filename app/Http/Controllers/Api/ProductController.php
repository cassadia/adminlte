<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserRoleService;
use App\Services\ContentService;
use App\Models\Product;

class ProductController extends Controller
{
    protected $userRoleService;

    public function __construct(UserRoleService $userRoleService)
    {
        $this->userRoleService = $userRoleService;
    }

    public function createProduct(Request $request)
    {
        $this->validate($request, [
            'KodeProduk' => 'required|min:5',
            'NamaProduk' => 'required|min:10'
        ]);

        // Periksa apakah kode produk sudah ada di database
        $existingProduct = Product::where('kd_produk', $request->KodeProduk)->first();

        // Jika kode produk sudah ada, tampilkan pesan kesalahan
        if ($existingProduct) {
            // return redirect()->back()->withInput()->withErrors(['KodeProduk' => 'Kode Produk sudah ada di database.'])->with(['error' => 'Kode Produk sudah ada di database.']);
            return response()->json([
                'status' => 'error',
                'message' => 'Kode Produk sudah ada di database.'
            ], 400);
        }

        // Tentukan nilai status berdasarkan kondisi checkbox
        $status = $request->has('status') ? 'Aktif' : 'Tidak Aktif';

        //create post
        Product::create([
            'kd_produk' => $request->KodeProduk,
            'nm_produk' => $request->NamaProduk,
            'qty_available' => $request->Qty,
            'harga_jual' => $request->HargaJual,
            'database' => $request->Lokasi,
            'status' => $status,
        ]);

        //redirect to index
        // return redirect()->route('product.index')->with(['success' => 'Data Berhasil Disimpan!']);
        return response()->json([
            'status' => 'success',
            'message' => 'Data Produk Berhasil Disimpan!'
        ], 200);
    }

    public function updateProduct(Request $request)
    {
        try {
            //get post by ID
            $product = Product::findOrFail($request->id);

            // Tentukan nilai status berdasarkan kondisi checkbox
            $status = $request->has('status') ? 'Aktif' : 'Tidak Aktif';

            //update product without image
            $product->update([
                'kd_produk' => $request->KodeProduk,
                'nm_produk' => $request->NamaProduk,
                'qty_available' => $request->Qty,
                'harga_jual' => $request->HargaJual,
                'database' => $request->Lokasi,
                'status' => $status,
            ]);

            //redirect to index
            // return redirect()->route('product.index')->with(['success' => 'Data Berhasil Diubah!']);
            return response()->json([
                'status' => 'success',
                'message' => 'Data Produk Berhasil Diubah!'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data Produk Tidak Ditemukan!'
            ], 404);
        }
    }

    public function deleteProduct(Request $request)
    {
        try {
            $products = Product::findOrFail($request->productId);
            $products->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data Produk Berhasil Dihapus!'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data Produk Tidak Ditemukan!'
            ], 404);
        }
    }
}
