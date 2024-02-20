<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class VehicleController extends Controller
{
    //
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $keyword = $request->input('keyword');

        $vehicles = Vehicle::whereNull('deleted_at')
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('no_seri_rangka', 'like', '%' . $keyword . '%')
                    ->orWhere('no_seri_mesin', 'like', '%' . $keyword . '%')
                    ->orWhere('nm_motor', 'like', '%' . $keyword . '%')
                    ->orWhere('status', 'like', '%' . $keyword . '%');
            })
            ->paginate($perPage);

        return view('vehicles.index', compact('vehicles'));
    }

    public function create(): View
    {
        return view('vehicles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'KodeMotor' => 'required|min:3',
            'NamaMotor' => 'required|min:5',
            'gambar' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Menambahkan validasi untuk tipe gambar dan ukuran
        ]);

        // Periksa apakah kode produk sudah ada di database
        // $existingNoSerMesin = Vehicle::where('no_seri_mesin', $request->NoSeriMesin)
        //                         ->orWhere('no_seri_rangka', $request->NoSeriRangka)
        //                         ->first();
        // $existingNoSerMesin = Vehicle::where('no_seri_mesin', $request->NoSeriMesin)->first();
        
        // Jika nomor seri mesin atau nomor seri rangka sudah ada, tampilkan pesan kesalahan
        // if ($existingNoSerMesin) {
        //     return redirect()->back()->withInput()->withErrors(['NoSeriMesin' => 'Nomor Seri Mesin sudah ada di database.'])->with(['error' => 'Nomor Seri Mesin sudah ada di database.']);
        // }

        // Periksa apakah kode produk sudah ada di database
        // $existingNoSeriRangka = Vehicle::where('no_seri_rangka', $request->NoSeriRangka)->first();

        // Jika kode produk sudah ada, tampilkan pesan kesalahan
        // if ($existingNoSeriRangka) {
        //     return redirect()->back()->withInput()->withErrors(['NoSeriRangka' => 'Nomor Seri Rangka sudah ada di database.'])->with(['error' => 'Nomor Seri Rangka sudah ada di database.']);
        // }

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

        //redirect to index
        return redirect()->route('vehicle.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function show(string $id): View
    {
        //get post by ID
        $vehicles = Vehicle::findOrFail($id);

        //render view with post
        return view('vehicles.show', compact('vehicles'));
    }

    public function edit(string $id): View
    {
        //get post by ID
        $vehicles = Vehicle::findOrFail($id);

        //render view with post
        return view('vehicles.edit', compact('vehicles'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        //validate form
        $this->validate($request, [
            'KodeMotor' => 'required|min:5',
            'NamaMotor' => 'required|min:10',
            'TahunMotor' => 'required|min:4',
            'NoSeriMesin' => 'required|min:10',
            'NoSeriRangka' => 'required|min:10'
        ]);

        //get post by ID
        $vehicles = Vehicle::findOrFail($id);

        // Tentukan nilai status berdasarkan kondisi checkbox
        $status = $request->has('status') ? 'Aktif' : 'Tidak Aktif';
                    
        //update vehi$vehicles without image
        $vehicles->update([
            'kd_motor' => $request->KodeMotor,
            'nm_motor' => $request->NamaMotor,
            'tahun' => $request->TahunMotor,
            'no_seri_mesin' => $request->NoSeriMesin,
            'no_seri_rangka' => $request->NoSeriRangka,
            'status' => $status,
        ]);

        //redirect to index
        return redirect()->route('vehicle.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    public function destroy($id): RedirectResponse
    {
        //get post by ID
        $vehicles = Vehicle::findOrFail($id);

        //delete post
        $vehicles->delete();

        //redirect to index
        return redirect()->route('vehicle.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
    
        $vehicles = Vehicle::whereNull('deleted_at')
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('no_seri_rangka', 'like', '%' . $keyword . '%')
                    ->orWhere('no_seri_mesin', 'like', '%' . $keyword . '%')
                    ->orWhere('nm_motor', 'like', '%' . $keyword . '%')
                    ->orWhere('status', 'like', '%' . $keyword . '%');
            })
            ->paginate(10); // Jumlah data per halaman
    
        return view('mapping.index', compact('vehicles'));
    }

    public function vehicleExport(Request $request)
    {
        $keyword = $request->input('keyword');
        
        if ($keyword) {
            $vehicles = Vehicle::where('nm_produk', 'like', '%' . $keyword . '%')->get();
        } else {
            $vehicles = Vehicle::all();
        }

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=vehicles.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $callback = function () use ($vehicles) {
            $file = fopen('php://output', 'w');
            fputcsv($file, array('Kode Motor', 'Nama Motor', 'Tahun Dari'
                , 'Tahun Sampai', 'No Seri Mesin', 'No Seri Rangka', 'Status'
                , 'Tanggal Buat', 'Tanggal Ubah'));

            foreach ($vehicles as $vehicle) {
                fputcsv($file, array($vehicle->kd_motor, $vehicle->nm_motor, $vehicle->tahun_dari
                    , $vehicle->tahun_sampai, $vehicle->no_seri_mesin, $vehicle->no_seri_rangka, $vehicle->status
                    , $vehicle->created_at, $vehicle->updated_at));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}