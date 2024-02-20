<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
//return type redirectResponse
use Illuminate\Http\RedirectResponse;
use App\Models\Product;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    //
    // public function show() {
    //     return view('product.product');
    // }

    // public function index(): View {
    //     $product = Product::latest()->paginate(5);
    //     return view('product.index', compact('product'));
    // }

    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $keyword = $request->input('keyword');

        $products = Product::whereNull('deleted_at')
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('kd_produk', 'like', '%' . $keyword . '%')
                    ->orWhere('nm_produk', 'like', '%' . $keyword . '%')
                    ->orWhere('status', 'like', '%' . $keyword . '%');
            })
            ->paginate($perPage);
        
        $products->appends(['keyword' => $keyword]);

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        return view('products.create');
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
        $products = Product::findOrFail($id);

        //render view with post
        return view('products.show', compact('products'));
    }

    public function edit(string $id): View
    {
        //get post by ID
        $products = Product::findOrFail($id);

        //render view with post
        return view('products.edit', compact('products'));
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
        print('$request' . $request);
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
 
            $data = Product::whereNull('deleted_at') // Menambahkan kondisi untuk memeriksa apakah deleted_at null
            ->where(function ($query) use ($request) {
                $query->where('id', 'like', '%' . $request->search . '%')
                    ->orWhere('kd_produk', 'like', '%' . $request->search . '%')
                    ->orWhere('nm_produk', 'like', '%' . $request->search . '%');
            })
            ->get();
 
            $output='';
            if (count($data)>0) {
                $output ='
                    <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Kode Produk</th>
                        <th scope="col">Nama Produk</th>
                        <th scope="col">Lokasi</th>
                    </tr>
                    </thead>
                    <tbody>';
                        foreach ($data as $row) {
                            $output .='
                            <tr>
                            <th scope="row">'.$row->id.'</th>
                            <td>'.$row->kd_produk.'</td>
                            <td>'.$row->nm_produk.'</td>
                            <td>'.$row->database.'</td>
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
        return view('mapping.index');
    }

    public function myNewFunction()
    {
        return view('mapping.index');
    }

    public function searchMotor(Request $request)
    {
        if ($request->ajax()) {
            // $data = Vehicle::leftJoin('mappings as b', function ($join) use ($request) {
            //         $join->on('b.kd_motor', '=', 'vehicles.kd_motor')
            //              ->where('b.kd_produk', '=', $request->kd_produk);
            //     })
            //     ->whereNull('b.deleted_at') // Where clause ini sekarang berhubungan dengan tabel mappings
            //     ->select('b.id', 'b.kd_produk', 'vehicles.nm_motor', 'vehicles.kd_motor', 'vehicles.tahun', 'vehicles.no_seri_mesin', 'vehicles.no_seri_rangka')
            //     ->get();

            $data = Vehicle::leftJoin('mappings as b', function ($join) use ($request) {
                    $join->on('b.kd_motor', '=', 'vehicles.kd_motor')
                         ->where('b.kd_produk', '=', $request->kd_produk);
                })
                ->select('b.id', DB::raw("CASE WHEN b.deleted_at IS NULL THEN b.kd_produk ELSE NULL END AS kdproduk")
                , 'vehicles.nm_motor', 'vehicles.kd_motor', 'vehicles.tahun_dari', 'vehicles.tahun_sampai', 'vehicles.no_seri_mesin', 'vehicles.no_seri_rangka')
                ->orderBy('kdproduk', 'DESC')
                ->get();

            $output='';
            if (count($data)>0) {
                foreach ($data as $row) {
                    $isChecked = !empty($row->kdproduk) ? 'checked' : '';
                    $output .='
                    <tr>
                        <td><input type="checkbox" name="motor_cek" class="motor_cek" value="' . $row->kdproduk . '" ' . $isChecked . ' data-id="'. $row->kd_motor .'"></td>
                        <td><input type="text" name="produk_kode" class="produk_kode" value="'.$row->kdproduk.'" data-id="'.$row->kdproduk.'" hidden>'.$row->kdproduk.'</td>
                        <td>'.$row->nm_motor.'</td>
                        <td>'.$row->kd_motor.'</td>
                        <td>'.$row->tahun_dari.'</td>
                        <td>'.$row->tahun_sampai.'</td>
                        <td>'.$row->no_seri_mesin.'</td>
                        <td>'.$row->no_seri_rangka.'</td>
                    </tr>
                    ';
                }
            } else {
                $output .='No results';
            }
            return $output;
        }
    }
}