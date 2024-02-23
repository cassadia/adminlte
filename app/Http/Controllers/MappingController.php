<?php

namespace App\Http\Controllers;
use App\Models\Mapping;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class MappingController extends Controller
{
    //
    public function store(Request $request): RedirectResponse
    {
        // dump('$request: ' . $request);
        dd($request->all());

        // $this->validate($request, [
        //     'KodeProduk' => 'required|min:5',
        //     'NamaProduk' => 'required|min:10'
        // ]);

        // Periksa apakah kode produk sudah ada di database
        // $existingProduct = Product::where('kd_produk', $request->KodeProduk)->first();

        // Jika kode produk sudah ada, tampilkan pesan kesalahan
        // if ($existingProduct) {
        //     return redirect()->back()->withInput()->withErrors(['KodeProduk' => 'Kode Produk sudah ada di database.'])->with(['error' => 'Kode Produk sudah ada di database.']);
        // }

        // Tentukan nilai status berdasarkan kondisi checkbox
        // $status = $request->has('status') ? 'Aktif' : 'Tidak Aktif';

        //create post
        // Product::create([
        //     'kd_produk' => $request->KodeProduk,
        //     'nm_produk' => $request->NamaProduk,
        //     'status' => $status,
        // ]);

        //redirect to index
        // return redirect()->route('product.index')->with(['success' => 'Data Berhasil Disimpan!']);
        // return view('mapping.index');
        return redirect()->route('product.mapping');
    }

    // public function updateMapping(Request $request)
    // {

    //     // $existingMapping = Mapping::whereNull('deleted_at')
    //     //     ->where(function ($query) use ($request) {
    //     //         $query->where('kd_motor', $request->id)
    //     //             ->where('kd_produk', $request->kdProduk);
    //     //     })
    //     //     ->get();

    //     $existingMapping = Mapping::where('kd_motor', $request->id)
    //         ->where('kd_produk', $request->kdProduk);

    //     // $existingMapping = Mapping::where('kd_motor', $request->id)
    //     // ->where('kd_produk', $request->kdProduk)
    //     // ->unless($request->has('exclude_deleted'), function ($query) {
    //     //     $query->whereNull('deleted_at');
    //     // })
    //     // ->first();

    //     // dd([
    //     //     'data' => $request->all(),
    //     //     'cek' => $existingMapping,
    //     //     'cik' => !$request->isChecked
    //     // ]);

    //     // Jika mapping ditemukan
    //     if ($existingMapping) {
    //         // dd([
    //         //     'insideExist' => $existingMapping
    //         // ]);
    //         // Jika deleted_at tidak null, maka update data mapping tersebut
    //         if ($existingMapping->deleted_at) {
    //             dd([
    //                 'ifExistMapp' => $existingMapping,
    //             ]);
    //             $existingMapping->update(['deleted_at' => null]); //
    //             return response()->json(['message' => 'Data berhasil di-restore'], 200);
    //         } else {
    //             // dd([
    //             //     'elseExistMapp' => $existingMapping,
    //             // ]);
    //             // Jika deleted_at null, maka set deleted_at menjadi waktu sekarang
    //             $existingMapping->update(['deleted_at' => now()]);
    //             return response()->json(['message' => 'Data berhasil dihapus'], 200);
    //         }
    //     } else {
    //         // Jika mapping tidak ditemukan, buat mapping baru
    //         Mapping::create([
    //             'kd_produk' => $request->kdProduk,
    //             'kd_motor' => $request->id,
    //         ]);
    //         return response()->json(['message' => 'Data berhasil disimpan'], 200);
    //     }
    // }

    public function updateMapping(Request $request)
    {
        $existingMapping = Mapping::withoutGlobalScopes()
            ->where('kd_motor', $request->id)
            ->where('kd_produk', $request->kdProduk)
            ->first();
    
        if ($existingMapping !== null) {
            // dd([
            //     'print' => $existingMapping
            // ]);
            if ($existingMapping->deleted_at) {
                $existingMapping->update(['deleted_at' => null]);
                return response()->json([
                    'code' => 'rest',
                    'message' => 'Data berhasil di-restore
                '], 200);
            } else {
                $existingMapping->update(['deleted_at' => now()]);
                return response()->json([
                    'code' => 'del',
                    'message' => 'Data berhasil dihapus'
                ], 200);
            }
        } else {
            Mapping::create([
                'kd_motor' => $request->id,
                'kd_produk' => $request->kdProduk,
            ]);
            return response()->json([
                'code' => 'crea',
                'message' => 'Data berhasil disimpan
            '], 200);
        }
    }

    public function updateMappingAll(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }
    
        $dataArray = $request->input('data');
        $response = ['message' => 'Tidak ada tindakan yang diambil']; // Pesan respons default
    
        foreach ($dataArray as $data) {
            $kdProduk = $data['kdProduk'];
            $kdProdukSelected = $data['kdProdukSelected'];
            $kdMotor = $data['kdMotor'];
    
            if ($kdProdukSelected == null) {
                $existingMapping = Mapping::withoutGlobalScopes()
                    ->where('kd_motor', $kdMotor)
                    ->where('kd_produk', $kdProduk)
                    ->first();
    
                if ($existingMapping !== null) {
                    if ($existingMapping->deleted_at) {
                        $existingMapping->update(['deleted_at' => null]);
                        $response = [
                            'code' => 'rest',
                            'message' => 'Data berhasil dikembalikan'
                        ];
                    }
                } else {
                    Mapping::create([
                        'kd_motor' => $kdMotor,
                        'kd_produk' => $kdProduk,
                    ]);
                    $response = [
                        'code' => 'crea',
                        'message' => 'Data berhasil disimpan'
                    ];
                }
            }
        }
    
        return response()->json($response, 200);
    }

    public function mappingExport(Request $request)
    {
        if ($request->ajax()) {
            $data = Vehicle::leftJoin('mappings as b', function ($join) use ($request) {
                $join->on('b.kd_motor', '=', 'vehicles.kd_motor')
                     ->where('b.kd_produk', '=', $request->kd_produk);
            })
            ->select(DB::raw("CASE WHEN b.deleted_at IS NULL THEN b.kd_produk ELSE NULL END AS kdproduk")
                , 'vehicles.nm_motor', 'vehicles.kd_motor'
                , DB::raw("CONCAT(vehicles.tahun_dari, '-', COALESCE(vehicles.tahun_sampai, 'Sekarang')) AS tahun_pembuatan")
                , 'vehicles.no_seri_mesin', 'vehicles.no_seri_rangka'
            )
            ->orderBy('kdproduk', 'DESC')
            ->get()
            ->toArray();

            // Buat file ekspor (misalnya, file CSV)
            $fileName = 'mapping_export.csv';
            $file = fopen('php://temp', 'w+');

            $headers = array(
                'Kode Produk',
                'Nama Motor',
                'Kode Motor',
                'Tahun Pembuatan',
                'No Seri Mesin',
                'No Seri Rangka'
            );

            fputcsv($file, $headers);

            // Tulis data ke file CSV
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            rewind($file);

            return response()->stream(function () use ($file) {
                fpassthru($file);
            }, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        }
    }

    // public function updateMappingAll(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $dataArray = $request->all()['data'];

    //         foreach ($dataArray as $data) {
    //             $kdProduk = $data['kdProduk'];
    //             $kdProdukSelected = $data['kdProdukSelected'];
    //             $kdMotor = $data['kdMotor'];

    //             if ($kdProdukSelected == null) {
    //                 $existingMapping = Mapping::withoutGlobalScopes()
    //                     ->where('kd_motor', $kdMotor)
    //                     ->where('kd_produk', $kdProduk)
    //                     ->first();

    //                 if ($existingMapping !== null) {
    //                     if ($existingMapping->deleted_at) {
    //                         $existingMapping->update(['deleted_at' => null]);
    //                         return response()->json([
    //                             'code' => 'del',
    //                             'message' => 'Data berhasil dihapus'
    //                         ], 200);
    //                     }
    //                 } else {
    //                     Mapping::create([
    //                         'kd_motor' => $kdMotor,
    //                         'kd_produk' => $kdProduk,
    //                     ]);
    //                     return response()->json([
    //                         'code' => 'crea',
    //                         'message' => 'Data berhasil disimpan
    //                     '], 200);
    //                 }
    //             }
    //         }
    //     }
    // }
}