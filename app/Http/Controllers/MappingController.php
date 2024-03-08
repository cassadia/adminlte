<?php

namespace App\Http\Controllers;
use App\Models\Mapping;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

use App\Services\UserRoleService;
use App\Services\ContentService;

class MappingController extends Controller
{
    protected $userRoleService;

    public  function __construct(UserRoleService $userRoleService)
    {
        $this->userRoleService = $userRoleService;
    }

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
        // dd($request->ajax());
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
            } else {
                $existingMapping = Mapping::withoutGlobalScopes()
                    ->where('kd_motor', $kdMotor)
                    ->where('kd_produk', $kdProduk)
                    ->first();
    
                if ($existingMapping !== null) {
                    if (!$existingMapping->deleted_at) {
                        $existingMapping->update(['deleted_at' => now()]);
                        $response = [
                            'code' => 'del',
                            'message' => 'Data berhasil dihapus'
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
}