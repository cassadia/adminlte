<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Mapping;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Models\Permission;

use App\Services\UserRoleService;
use App\Services\ContentService;

class VehicleController extends Controller
{
    protected $userRoleService;

    public  function __construct(UserRoleService $userRoleService)
    {
        $this->userRoleService = $userRoleService;
    }

    public function index(Request $request)
    {
        $perPage = $request->input('perPage');
        $keyword = $request->input('keyword');

        $sort = $request->input('sort', 'kd_motor');
        $order = $request->input('order');

        $query = Vehicle::when($keyword, function ($query) use ($keyword) {
            return $query->where('no_seri_rangka', 'like', '%' . $keyword . '%')
                ->orWhere('no_seri_mesin', 'like', '%' . $keyword . '%')
                ->orWhere('nm_motor', 'like', '%' . $keyword . '%')
                ->orWhere('status', 'like', '%' . $keyword . '%');
        });

        if ($order == 'asc' || $order == 'desc') {
            $vehicles = $query->orderBy($sort, $order)->paginate($perPage);
        } else {
            $vehicles = $query->paginate($perPage);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'vehicles' => $vehicles,
                'order' => $order,
                'sort' => $sort
            ]
        ], 200);
    }

    public function getVehicleById(Request $request)
    {
        $id = $request->input('id');

        $vehicles = Vehicle::findOrFail($id);

        if ($vehicles) {
            return response()->json([
                'status' => 'success',
                'data' => $vehicles
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Vehicle not found'
        ], 404);
    }

    public function createVehicle(Request $request)
    {
        // dd($request)->all();
        // Tentukan nilai status berdasarkan kondisi checkbox
        $status = $request->has('status') ? 'Aktif' : 'Tidak Aktif';

        if ($request->hasFile('gambar')) {
            // Dapatkan file yang diunggah
            $file = $request->file('gambar');

            // Buat nama unik untuk file
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Simpan file ke dalam direktori penyimpanan (misalnya: public/images)
            $file->move(public_path('images'), $fileName);

            // Simpan data ke dalam database termasuk nama file gambar
            Vehicle::create([
                'kd_motor' => $request->KodeMotor,
                'nm_motor' => $request->NamaMotor,
                'tahun_dari' => $request->TahunMotorDari,
                'tahun_sampai' => $request->TahunMotorSampai,
                'no_seri_mesin' => $request->NoSeriMesin,
                'no_seri_rangka' => $request->NoSeriRangka,
                'status' => $status,
                'gambar' => $fileName, // Simpan nama file gambar ke dalam database
            ]);
        } else {
            //create post
            Vehicle::create([
                'kd_motor' => $request->KodeMotor,
                'nm_motor' => $request->NamaMotor,
                'tahun_dari' => $request->TahunMotorDari,
                'tahun_sampai' => $request->TahunMotorSampai,
                'no_seri_mesin' => $request->NoSeriMesin,
                'no_seri_rangka' => $request->NoSeriRangka,
                'status' => $status,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data Vehicle Berhasil Disimpan!'
        ], 200);
    }

    public function updateVehicle(Request $request)
    {
        //get post by ID
        $vehicles = Vehicle::findOrFail($request->id);

        // Tentukan nilai status berdasarkan kondisi checkbox
        $status = $request->has('status') ? 'Aktif' : 'Tidak Aktif';

        if ($vehicles) {
            $checkMapping = Mapping::whereNull('deleted_at')
            ->where([
                'kd_motor' => $vehicles->kd_motor,
                'id_motor' => $vehicles->id
            ])
            ->first();

            if ($checkMapping && $status == 'Tidak Aktif') {
                $checkMapping->delete();
            }
        }

        if ($request->hasFile('gambar')) {
            $this->validate($request, [
                'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Menambahkan validasi untuk tipe gambar dan ukuran
            ]);
        }

        if (!$request->hasFile('gambar') && $request->existing_gambar) {
            $allowedExtensions = ['jpg', 'png', 'jpeg', 'gif'];
            $fileExtension = pathinfo($request->existing_gambar, PATHINFO_EXTENSION);

            if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File gambar tidak valid'
                ], 400);
            }

            $gambarPath = $request->existing_gambar;
        }

        if ($request->hasFile('gambar')) {
            // Dapatkan file yang diunggah
            $file = $request->file('gambar');

            // Buat nama unik untuk file
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Simpan file ke dalam direktori penyimpanan (misalnya: public/images)
            $file->move(public_path('images'), $fileName);
            // Simpan data ke dalam database termasuk nama file gambar
            $vehicles->update([
                'kd_motor' => $request->KodeMotor,
                'nm_motor' => $request->NamaMotor,
                'tahun_dari' => $request->TahunMotorDari,
                'tahun_sampai' => $request->TahunMotorSampai,
                'no_seri_mesin' => $request->NoSeriMesin,
                'no_seri_rangka' => $request->NoSeriRangka,
                'status' => $status,
                'gambar' => $fileName, // Simpan nama file gambar ke dalam database
                'deleted_at' => null,
            ]);
        } else {
            //create post
            $vehicles->update([
                'kd_motor' => $request->KodeMotor,
                'nm_motor' => $request->NamaMotor,
                'tahun_dari' => $request->TahunMotorDari,
                'tahun_sampai' => $request->TahunMotorSampai,
                'no_seri_mesin' => $request->NoSeriMesin,
                'no_seri_rangka' => $request->NoSeriRangka,
                'status' => $status,
                'deleted_at' => null,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data Vehicle Berhasil Diubah!'
        ], 200);
    }

    public function deleteVehicle(Request $request)
    {
        try {
            $vehicles = Vehicle::find($request->vehicleId);

            if ($vehicles) {
                $checkMapping = Mapping::whereNull('deleted_at')
                    ->where([
                        'kd_motor' => $vehicles->kd_motor,
                        'id_motor' => $vehicles->id
                    ])
                    ->first();

                if ($checkMapping) {
                    $checkMapping->delete();
                }

                $vehicles->delete();
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Data Vehicle Berhasil Dihapus!'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data Vehicle Gagal Dihapus!'
            ], 400);
        }
    }

}
