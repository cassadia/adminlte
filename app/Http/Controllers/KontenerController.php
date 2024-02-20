<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class KontenerController extends Controller
{
    //
    public function index()
    {
        return view('kontener.index');
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