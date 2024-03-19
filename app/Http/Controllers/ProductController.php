<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
//return type redirectResponse
use Illuminate\Http\RedirectResponse;
use App\Models\Product;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

use App\Services\UserRoleService;
use App\Services\ContentService;

class ProductController extends Controller
{
    protected $userRoleService;

    public  function __construct(UserRoleService $userRoleService)
    {
        $this->userRoleService = $userRoleService;
    }

    public function index(Request $request)
    {
        $emailUser = auth()->user()->email;

        $perPage = $request->input('perPage', 10);
        $keyword = $request->input('keyword');

        $products = Product::join('accurate_db as b', function ($join) use ($request) {
            $join->on('b.kd_database', '=', 'products.database');
        })
            ->whereNull('products.deleted_at')
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('products.kd_produk', 'like', '%' . $keyword . '%')
                    ->orWhere('products.nm_produk', 'like', '%' . $keyword . '%')
                    ->orWhere('products.status', 'like', '%' . $keyword . '%')
                    ->orWhere('products.barcode', 'like', '%' . $keyword . '%');
            })
            ->select('products.id', 'products.kd_produk', 'products.nm_produk', 'products.qty_available', 'products.harga_jual', 'products.status', 'b.nm_database', 'products.barcode')
            ->paginate($perPage);
        
        $products->appends(['keyword' => $keyword]);
        $menusdua = $this->userRoleService->getUserRole($emailUser);
        $content = ContentService::getContent();

        return view('products.index', compact('products', 'menusdua', 'content'));
    }

    public function create(): View
    {
        $emailUser = auth()->user()->email;
        $menusdua = $this->userRoleService->getUserRole($emailUser);
        $content = ContentService::getContent();
        $menuLokasi = DB::table('accurate_db')->whereNull('deleted_at')->get();

        return view('products.create', compact('menusdua', 'content', 'menuLokasi'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'KodeProduk' => 'required|min:5',
            'NamaProduk' => 'required|min:10'
        ]);

        // Periksa apakah kode produk sudah ada di database
        $existingProduct = Product::where('kd_produk', $request->KodeProduk)->first();

        // Jika kode produk sudah ada, tampilkan pesan kesalahan
        if ($existingProduct) {
            return redirect()->back()->withInput()->withErrors(['KodeProduk' => 'Kode Produk sudah ada di database.'])->with(['error' => 'Kode Produk sudah ada di database.']);
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
        return redirect()->route('product.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function show(string $id): View
    {
        //get post by ID
        $products = Product::join('accurate_db as b', function ($join) {
            $join->on('b.kd_database', '=', 'products.database');
        })
        ->select('products.id', 'products.kd_produk', 'products.nm_produk', 'products.qty_available', 'products.harga_jual', 'products.status', 'b.nm_database')
        ->findOrFail($id);
        Session::put('previous_url', url()->previous());

        $emailUser = auth()->user()->email;
        $menusdua = $this->userRoleService->getUserRole($emailUser);
        $content = ContentService::getContent();

        //render view with post
        return view('products.show', compact('products', 'menusdua', 'content'));
    }

    public function edit(string $id): View
    {
        //get post by ID
        $products = Product::Join('accurate_db as b', function ($join) {
            $join->on('b.kd_database', '=', 'products.database');
        })
        ->select('products.*', 'b.nm_database')
        ->findOrFail($id);

        $getLokasi = DB::table('accurate_db')
            ->whereNull('deleted_at')
            ->get();

        $emailUser = auth()->user()->email;
        $menusdua = $this->userRoleService->getUserRole($emailUser);
        $content = ContentService::getContent();

        //render view with post
        return view('products.edit', compact('products', 'menusdua', 'content', 'getLokasi'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        //validate form
        $this->validate($request, [
            'KodeProduk' => 'required|min:5',
            'NamaProduk' => 'required|min:10'
        ]);

        //get post by ID
        $product = Product::findOrFail($id);

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
        return redirect()->route('product.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    public function destroy($id): RedirectResponse
    {
        //get post by ID
        $products = Product::findOrFail($id);

        //delete post
        $products->delete();

        //redirect to index
        return redirect()->route('product.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
    
        // Lakukan pencarian berdasarkan keyword dan kirimkan saran kembali dalam bentuk JSON
        $suggestions = Product::where('kd_produk', 'like', '%' . $keyword . '%')
            ->orWhere('nm_produk', 'like', '%' . $keyword . '%')
            ->limit(10) // Batasi jumlah saran yang dikembalikan
            ->get();
    
        return response()->json($suggestions);
    }

    public function searchAuto(Request $request)
    {
 
        if ($request->ajax()) {
 
            // $data = Product::whereNull('deleted_at') // Menambahkan kondisi untuk memeriksa apakah deleted_at null
            // ->where(function ($query) use ($request) {
            //     $query->where('id', 'like', '%' . $request->search . '%')
            //         ->orWhere('kd_produk', 'like', '%' . $request->search . '%')
            //         ->orWhere('nm_produk', 'like', '%' . $request->search . '%');
            // })
            // ->get();

            $data = DB::table('products as p')
                ->select('p.kd_produk', 'p.nm_produk')
                ->whereNull('p.deleted_at')
                ->where(function ($query) use ($request) {
                    $query->where('p.kd_produk', 'like', '%' . $request->search . '%')
                        ->orWhere('p.nm_produk', 'like', '%' . $request->search . '%');
                })
                ->distinct()
                ->get();

            $jml = 1;
 
            $output='';
            if (count($data)>0) {
                $output ='
                    <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Kode Produk</th>
                        <th scope="col">Nama Produk</th>
                    </tr>
                    </thead>
                    <tbody>';
                        foreach ($data as $row) {
                            $output .='
                            <tr>
                                <th scope="row">'.$jml++.'</th>
                                <td>'.$row->kd_produk.'</td>
                                <td>'.$row->nm_produk.'</td>
                            </tr>
                            ';
                        }
                $output .= '
                    </tbody>
                    </table>';
            } else {
                $output .='No results';
            }
            return $output;
        }
    }

    public function indexMapping()
    {
        $emailUser = auth()->user()->email;
        $menusdua = $this->userRoleService->getUserRole($emailUser);
        $content = ContentService::getContent();
        
        return view('mapping.index', compact('menusdua', 'content'));
    }

    public function myNewFunction()
    {
        return view('mapping.index');
    }

    // public function searchMotor(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $data = Vehicle::leftJoin('mappings as b', function ($join) use ($request) {
    //                 $join->on('b.kd_motor', '=', 'vehicles.kd_motor')
    //                      ->where('b.kd_produk', '=', $request->kd_produk);
    //             })
    //             ->select('b.id', DB::raw("CASE WHEN b.deleted_at IS NULL THEN b.kd_produk ELSE NULL END AS kdproduk")
    //             , 'vehicles.nm_motor', 'vehicles.kd_motor', 'vehicles.tahun_dari', 'vehicles.tahun_sampai', 'vehicles.no_seri_mesin', 'vehicles.no_seri_rangka')
    //             ->orderBy('kdproduk', 'DESC')
    //             ->get();

    //         $output='';
    //         if (count($data)>0) {
    //             foreach ($data as $row) {
    //                 $isChecked = !empty($row->kdproduk) ? 'checked' : '';
    //                 $isCheckedBefore = !empty($row->kdproduk) ? 'true' : 'false';
    //                 $tahun_sampai = $row->tahun_sampai ?: 'Sekarang';
    //                 $tahun = $row->tahun_dari ? $row->tahun_dari . '-' . $tahun_sampai : '';
    //                 $output .='
    //                 <tr>
    //                     <td>
    //                         <input type="checkbox" name="motor_cek" class="motor_cek"
    //                             value="' . $row->kdproduk . '" ' . $isChecked . ' data-id="'. $row->kd_motor .'" data-checked-before="'. $isCheckedBefore .'">
    //                     </td>
    //                     <td>
    //                         <input type="text" name="produk_kode" class="produk_kode"
    //                             value="'.$row->kdproduk.'" data-id="'.$row->kdproduk.'" hidden>
    //                             '.$row->kdproduk.'
    //                     </td>
    //                     <td>'.$row->nm_motor.'</td>
    //                     <td>'.$row->kd_motor.'</td>
    //                     <td>'.$tahun.'</td>
    //                     <td>'.$row->no_seri_mesin.'</td>
    //                     <td>'.$row->no_seri_rangka.'</td>
    //                 </tr>
    //                 ';
    //             }
    //         } else {
    //             $output .='No results';
    //         }
    //         return $output;
    //     }
    // }

    public function searchMotor(Request $request)
    {
        if (!$request->ajax()) {
            return '';
        }
    
        $data = $this->fetchVehicleData($request);
    
        if (count($data) > 0) {
            return $this->generateOutput($data);
        } else {
            return 'No results';
        }
    }
    
    private function fetchVehicleData(Request $request)
    {
        return Vehicle::leftJoin('mappings as b', function ($join) use ($request) {
                $join->on('b.kd_motor', '=', 'vehicles.kd_motor')
                    ->on('b.id_motor', '=', 'vehicles.id')
                    ->where('b.kd_produk', '=', $request->kd_produk);
            })
            ->select('b.id', DB::raw("CASE WHEN b.deleted_at IS NULL THEN b.kd_produk ELSE NULL END AS kdproduk")
                , 'vehicles.id as id_motor', 'vehicles.nm_motor', 'vehicles.kd_motor', 'vehicles.tahun_dari', 'vehicles.tahun_sampai'
                , 'vehicles.no_seri_mesin', 'vehicles.no_seri_rangka'
            )
            ->where('vehicles.status', 'Aktif')
            ->orderBy('nm_motor', 'ASC')
            ->get();
    }
    
    private function generateOutput($data)
    {
        $output = '';
        $tdSeparator = "</td><td>";
    
        foreach ($data as $row) {
            $isChecked = !empty($row->kdproduk) ? 'checked' : '';
            $isCheckedBefore = !empty($row->kdproduk) ? 'true' : 'false';
            $tahun_sampai = $row->tahun_sampai ?: 'Sekarang';
            $tahun = $row->tahun_dari ? $row->tahun_dari . '-' . $tahun_sampai : '';
            $idMotor = $row->id_motor;
            $output .='
            <tr>
                <td>
                    <input type="checkbox" name="motor_cek" class="motor_cek"
                        value="' . $row->kdproduk . '" ' . $isChecked . '
                            data-id="'. $row->kd_motor .'" data-checked-before="'. $isCheckedBefore .'" data-id-motor="'. $idMotor .'">
                </td>
                <td>
                    <input type="text" name="produk_kode" class="produk_kode"
                        value="'.$row->kdproduk.'" data-id="'.$row->kdproduk.'" hidden>
                        '.$row->kdproduk.'
                </td>
                <td>'.$row->nm_motor.'</td>
                . $tdSeparator .
                <td>'.$row->kd_motor.'</td>
                <td>'.$tahun.'</td>
                <td>'.$row->no_seri_mesin.'</td>
                . $tdSeparator .
                <td>'.$row->no_seri_rangka.'</td>
            </tr>
            ';
        }
    
        return $output;
    }

    public function productExport(Request $request)
    {
        $keyword = $request->input('keyword');
        
        if ($keyword) {
            $products = Product::where('nm_produk', 'like', '%' . $keyword . '%')->get();
        } else {
            $products = Product::all();
        }

        // Buat header untuk file CSV
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=products.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        // Buat file CSV dengan menggunakan library PHP League CSV
        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');
            fputcsv($file, array('Kode Produk', 'Nama Produk', 'Qty'
                , 'Harga', 'Status', 'Tanggal Buat', 'Tanggal Ubah'));

            foreach ($products as $product) {
                fputcsv($file, array($product->kd_produk, $product->nm_produk, $product->qty_available
                    , $product->harga_jual, $product->status, $product->created_at, $product->updated_at));
            }

            fclose($file);
        };

        // Kembalikan response dengan header yang telah dibuat dan file yang telah dibuat
        return response()->stream($callback, 200, $headers);
    }
}