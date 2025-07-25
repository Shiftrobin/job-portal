<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //users list
    public function index(){
        $users = User::where('role', 'user')->orderBy('created_at','DESC')->paginate(10);
        return view('admin.users.list',['users' => $users]);
    }

    //Edit User
    public function Edit($id){

       $user = User::findOrFail($id);

        return view('admin.users.edit', [
            'user'=> $user
        ]);
    }

    //Update User
    public function update($id, Request $request){

        $user = User::findOrFail($id);
       // dd($id);

        $vlidator = Validator::make($request->all(),[
            'name'=> 'required|min:5|max:20',
            'email'=> 'required|email|unique:users,email,'.$user->id.',id',
        ]);

        if ($vlidator->passes()){
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->designation = $request->designation;
            $user->save();

            session()->flash('success', 'User Information Updated Successfully');

            return response()->json([
                'status' => true,
                'errors' => []
            ]);

        } else{
            return response()->json([
                'status' => false,
                'errors' => $vlidator->errors()
            ]);
        }
    }

    //Delete User
    public function destroy(Request $request){

        $id = $request->id;

       $user = User::find($id);

        if ($id == null){
            session()->flash('error','User not found');
            return response()->json([
                'status' => false,
            ]);
        }

        $user->delete();
        session()->flash('success','User deleted successfully');
        return response()->json([
            'status'=> true
            ]
        );


    }

}
