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
        $perPage = $request->input('perPage', 5);
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
            'TahunMotor' => 'required|min:4'
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

        //create post
        Vehicle::create([
            'kd_motor' => $request->KodeMotor,
            'nm_motor' => $request->NamaMotor,
            'tahun' => $request->TahunMotor,
            'no_seri_mesin' => $request->NoSeriMesin,
            'no_seri_rangka' => $request->NoSeriRangka,
            'status' => $status,
        ]);

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
}