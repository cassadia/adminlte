<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

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
            'nmUser' => 'required|min:5',
            'emailUser' => 'required|min:10|email', // Ubah 'email' menjadi 'emailUser' dan tambahkan unique:users,email
            'password' => 'required|confirmed|min:6', // Gunakan 'confirmed' untuk memastikan bahwa 'Password' dan 'KonfPassword' sama
        ], [
            'nmUser.required' => 'Nama tidak boleh kosong.',
            'nmUser.min' => 'Nama minimal harus terdiri dari 5 karakter.',
            'emailUser.required' => 'Email tidak boleh kosong.',
            'emailUser.min' => 'Email minimal harus terdiri dari 10 karakter.',
            'emailUser.email' => 'Email harus dalam format yang benar.',
            'emailUser.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password tidak boleh kosong.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal harus terdiri dari 6 karakter.',
        ]);

        // Periksa apakah kode produk sudah ada di database
        $existingUser = User::withoutGlobalScopes()
            ->where('email', $request->emailUser)->first();

        // Jika kode produk sudah ada, tampilkan pesan kesalahan
        if ($existingUser) {
            return redirect()->back()->withInput()->withErrors(['emailUser' => 'Email sudah ada di database!'])->with(['error' => 'Email sudah ada di database!']);
        }

        // Tentukan nilai status berdasarkan kondisi checkbox
        $status = $request->has('status') ? 'Aktif' : 'Tidak Aktif';

        //create post
        User::create([
            'name' => $request->nmUser,
            'email' => $request->emailUser,
            'password' => Hash::make($request->password),
            'status' => $status,
        ]);

        //redirect to index
        return redirect()->route('users.index')->with(['success' => 'Data Berhasil Disimpan!']);
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

    public function update(Request $request): RedirectResponse
    {
        //validate form
        $this->validate($request, [
            'nmUser' => 'required|min:5',
            'emailUser' => 'required|min:10|email', // Ubah 'email' menjadi 'emailUser' dan tambahkan unique:users,email
        ], [
            'nmUser.required' => 'Nama tidak boleh kosong.',
            'nmUser.min' => 'Nama minimal harus terdiri dari 5 karakter.',
            'emailUser.required' => 'Email tidak boleh kosong.',
            'emailUser.min' => 'Email minimal harus terdiri dari 10 karakter.',
            'emailUser.email' => 'Email harus dalam format yang benar.',
            'emailUser.unique' => 'Email sudah digunakan.',
        ]);


        // Tentukan nilai status berdasarkan kondisi checkbox
        $status = $request->has('status') ? 'Aktif' : 'Tidak Aktif';
                    
        //update product without image
        User::where("email", $request->emailUser)->update([
            'name' => $request->nmUser,
            'email' => $request->emailUser,
            'status' => $status,
        ]);

        //redirect to index
        return redirect()->route('users.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    public function destroy($id): RedirectResponse
    {
        //get post by ID
        $users = User::findOrFail($id);

        //delete post
        $users->delete();

        //redirect to index
        return redirect()->route('users.index')->with(['success' => 'Data Berhasil Dihapus!']);
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
