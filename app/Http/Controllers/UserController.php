<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
//return type redirectResponse
use Illuminate\Http\RedirectResponse;

use App\Models\User;

class UserController extends Controller
{
    // public function index()
    // {
    //     $users = User::paginate();

    //     return view('users.index', compact('users'));
    // }

    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 5);
        $keyword = $request->input('keyword');

        $users = User::whereNull('deleted_at')
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%');
            })
            ->paginate($perPage);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'KodeProduk' => 'required|min:5',
            'NamaProduk' => 'required|min:10'
        ]);

        // Periksa apakah kode produk sudah ada di database
        $existingProduct = User::where('kd_produk', $request->KodeProduk)->first();

        // Jika kode produk sudah ada, tampilkan pesan kesalahan
        if ($existingProduct) {
            return redirect()->back()->withInput()->withErrors(['KodeProduk' => 'Kode Produk sudah ada di database.'])->with(['error' => 'Kode Produk sudah ada di database.']);
        }

        // Tentukan nilai status berdasarkan kondisi checkbox
        $status = $request->has('status') ? 'Aktif' : 'Tidak Aktif';

        //create post
        User::create([
            'kd_produk' => $request->KodeProduk,
            'nm_produk' => $request->NamaProduk,
            'status' => $status,
        ]);

        //redirect to index
        return redirect()->route('product.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function show(string $id): View
    {
        //get post by ID
        $users = User::findOrFail($id);

        //render view with post
        return view('users.show', compact('users'));
    }

    public function edit(string $id): View
    {
        //get post by ID
        $users = User::findOrFail($id);

        //render view with post
        return view('users.edit', compact('users'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        //validate form
        $this->validate($request, [
            'KodeProduk' => 'required|min:5',
            'NamaProduk' => 'required|min:10'
        ]);

        //get post by ID
        $product = User::findOrFail($id);

        // Tentukan nilai status berdasarkan kondisi checkbox
        $status = $request->has('status') ? 'Aktif' : 'Tidak Aktif';
                    
        //update product without image
        $product->update([
            'kd_produk' => $request->KodeProduk,
            'nm_produk' => $request->NamaProduk,
            'status' => $status,
        ]);

        //redirect to index
        return redirect()->route('product.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    public function destroy($id): RedirectResponse
    {
        //get post by ID
        $users = User::findOrFail($id);

        //delete post
        $users->delete();

        //redirect to index
        return redirect()->route('product.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }

    public function search(Request $request)
    {
 
        if ($request->ajax()) {
 
            $data=User::where('id', 'like', '%'.$request->search.'%')
            ->orwhere('name', 'like', '%'.$request->search.'%')
            ->orwhere('email', 'like', '%'.$request->search.'%')->get();
 
            $output='';
            if (count($data)>0) {
                $output ='
                    <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                    </tr>
                    </thead>
                    <tbody>';
                        foreach ($data as $row) {
                            $output .='
                            <tr>
                            <th scope="row">'.$row->id.'</th>
                            <td>'.$row->name.'</td>
                            <td>'.$row->email.'</td>
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
}
