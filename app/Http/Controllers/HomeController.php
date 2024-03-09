<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Product;
use App\Models\Vehicle;
use App\Models\Mapping;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

use App\Services\UserRoleService;
use App\Services\ContentService;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    protected $userRoleService;
    
    public function __construct(UserRoleService $userRoleService)
    {
        $this->middleware('auth');
        $this->userRoleService = $userRoleService;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // $perPage = $request->input('perPage', 10);
        // $keyword = $request->input('keyword');

        // $products = Product::whereNull('deleted_at')
        //     ->when($keyword, function ($query) use ($keyword) {
        //         return $query->where('kd_produk', 'like', '%' . $keyword . '%')
        //             ->orWhere('nm_produk', 'like', '%' . $keyword . '%')
        //             ->orWhere('status', 'like', '%' . $keyword . '%');
        //     })
        //     ->paginate($perPage);
        
        // $products->appends(['keyword' => $keyword]);

        // $kdProduk = $request->input('kd_produk');

        $emailUser = auth()->user()->email;

        if ($request->has('reset')) {
            return redirect()->route('home');
        }

        if ($request->filled('keyCrProd') || $request->filled('keyNmPro') || $request->filled('keyNmMtr') || $request->filled('keyThn')) {
            return $this->search($request);
        }

        $mergedData = [];
        $menusdua = $this->userRoleService->getUserRole($emailUser);
        $content = ContentService::getContent();

        return view('home', compact('mergedData', 'menusdua', 'content'));

        // return view('home');
    }

    public function search(Request $request)
    {
        $emailUser = auth()->user()->email;
        // Eksekusi query
        // $query = DB::table('mappings as a')
        //                 ->select('a.kd_produk as Kode Barang', 'b.nm_produk as Nama Barang', 'c.nm_motor as Model', 'c.tahun_dari as Dari', 'c.tahun_sampai as Sampai', 'b.harga_jual as Harga', 'b.qty_available as Stock')
        //                 ->join('products as b', 'b.kd_produk', '=', 'a.kd_produk')
        //                 ->join('vehicles as c', 'c.kd_motor', '=', 'a.kd_motor')
        //                 ->distinct();

        $query = DB::table('mappings as a')
            ->join(DB::raw('(SELECT b.kd_produk, b.nm_produk, b.harga_jual, SUM(b.qty_available) AS qty_available  
                            FROM products b
                            GROUP BY b.kd_produk, b.nm_produk, b.harga_jual) AS b'), 'b.kd_produk', '=', 'a.kd_produk'
                        )
            ->join('vehicles as c', 'c.kd_motor', '=', 'a.kd_motor')
            ->select('a.kd_produk as Kode Barang', 'b.nm_produk as Nama Barang', 'c.nm_motor as Model'
            , 'c.tahun_dari as Dari', 'c.tahun_sampai as Sampai', 'b.harga_jual as Harga', 'b.qty_available as Stock')
            ->whereNull('a.deleted_at');

        if ($request->filled('keyCrProd')) {
            $query->where('a.kd_produk', 'like', '%' . $request->keyCrProd . '%');
        }

        if ($request->filled('keyNmPro')) {
            $query->where('b.nm_produk', 'like', '%' . $request->keyNmPro . '%');
        }

        if ($request->filled('keyNmMtr')) {
            $query->where('c.nm_motor', 'like', '%' . $request->keyNmMtr . '%');
        }

        if ($request->filled('keyThn')) {
            $query->where('c.tahun_dari', '<=', $request->keyThn)
                  ->where('c.tahun_sampai', '>=', $request->keyThn);
        }

        $mappingData = $query->get();
        // var_dump($query->toSql());
        // var_dump($request->keyCrProd);
        $mergedData = $this->prepareMergedData($mappingData);

        $menusdua = $this->userRoleService->getUserRole($emailUser);
        $content = ContentService::getContent();

        // Kirim data ke view
        return view('home', compact('mergedData', 'menusdua', 'content'));
    }

    // Metode untuk mendapatkan data produk berdasarkan kd_produk
    private function getProductData($kd_produk)
    {
        return DB::table('products')
                    ->select('database', 'kd_produk')
                    ->whereNotNull('database')
                    ->where('kd_produk', $kd_produk)
                    ->get();
    }

    private function prepareMergedData($mappingData)
    {
        $mergedData = [];
        $productDataCache = [];
    
        foreach ($mappingData as $mapping) {
            $kd_produk = $mapping->{'Kode Barang'};
    
            // Cek apakah data produk sudah ada di cache
            if (!isset($productDataCache[$kd_produk])) {
                $productData = $this->getProductData($kd_produk);
    
                // Jika data produk ditemukan, simpan di cache
                if ($productData->isNotEmpty()) {
                    $productDataCache[$kd_produk] = $productData;
                } else {
                    // Jika data produk tidak ditemukan, lanjutkan ke entri berikutnya
                    continue;
                }
            }
    
            // Gunakan data produk dari cache
            $productData = $productDataCache[$kd_produk];
    
            $mergedData[] = [
                'mapping' => $mapping,
                'productData' => $productData,
            ];
        }
    
        return $mergedData;
    }

    public function insertTransaction(Request $request)
    {
        $updQty = $request->stock - $request->qty;

        try {
            Product::where('kd_produk', $request->kdBarang)
            ->where('database', $request->lokasi)
            ->update([
                'qty_available' => $updQty
            ]);

            Transaction::create([
                'kd_produk' => $request->kdBarang,
                'kd_motor' => $request->mdlMotor,
                'harga_jual' => $request->hrgBarang,
                'kd_database' => $request->lokasi,
                'qty' => $request->qty,
            ]);

            return response()->json([
                'code' => 200,
                'message' => 'Data berhasil disimpan!'
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
