<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class DashboardController extends Controller
{
    //Dashboard
    public function index() {

        $jobCount = Job::count();
        $userCount = User::where('role','user')->count();
        $applicationCount = Application::count();

        return view("admin.dashboard", [
            "jobCount"=> $jobCount,
            "userCount"=> $userCount,
            "applicationCount"=> $applicationCount
            ]);
    }

    //admin login
    public function AdminLogin(){
        return view('admin.account.login');
    }

     //account authenticate
     public function AdminAuthenticate(Request $request)
     {
         $validator = Validator::make($request->all(), [
             'email'    => 'required|email',
             'password' => 'required',
         ]);

         if ($validator->fails()) {
             return redirect()->route('admin.login')
                 ->withErrors($validator)
                 ->withInput($request->only('email'));
         }

         if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
             if (Auth::user()->role === 'admin') {
                 return redirect()->route('admin.dashboard');
             } else {
                 Auth::logout();
                 return redirect()->route('admin.login')->with('error', 'Access denied. Admins only.');
             }
         }

         return redirect()->route('admin.login')->with('error', 'Invalid email or password.');
     }


    //admin logout
    public function AdminLogout(Request $request){
        Auth::logout();
        return redirect()->route('admin.login');
    }


    //Admin profile
    public function AdminProfile(Request $request){

        $id = Auth::user()->id;

        $user = User::where('id', $id)->where('role', 'admin')->first();

        return view('admin.account.profile', ['user'=> $user]);
      }


      //Admin Update profile
      public function AdminUpdateProfile(Request $request){

          $id = Auth::user()->id;

          $vlidator = Validator::make($request->all(),[
              'name'=> 'required|min:5|max:20',
              'email'=> 'required|email|unique:users,email,'.$id.',id',
          ]);

          if ($vlidator->passes()){

              $user = User::where('id', $id)->where('role', 'admin')->first();
              $user->name = $request->name;
              $user->email = $request->email;
              $user->mobile = $request->mobile;
              $user->designation = $request->designation;
              $user->save();

              session()->flash('success', 'Profile Updated Successfully');

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


    //admin update profile picture
    public function AdminUpdateProfilePic(Request $request){

        $id = Auth::user()->id;

        $validator = Validator::make($request->all(),[
            'image'=> 'required|image',
        ]);

        if ($validator->passes()){

            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $imageName = $id.'-'.time().'.'.$ext;
            $image->move(public_path('/upload/profile_pic/'), $imageName);

            //Create a small thumbnail
            $sourcePath = public_path('/upload/profile_pic/'.$imageName);
            $manager = new ImageManager(Driver::class);
            $image = $manager->read( $sourcePath);

            //crop the image
            $image->cover(150, height: 150);
            $image->toPng()->save(public_path('/upload/profile_pic/thumb/'.$imageName));


            //Delete old profile pic
            File::delete(public_path('/upload/profile_pic/thumb/'.AutH::user()->image));
            File::delete(public_path('/upload/profile_pic/'.AutH::user()->image));

            User::where('id', $id)->update([
                'image'=> $imageName,
            ]);

            session()->flash('success','Profile picture updated successfully');

            return response()->json([
                'status'=> true,
                'errors'=> []
            ]);

        } else {
            return response()->json([
                'status'=> false,
                'errors'=> $validator->errors()
            ]);
        }
    }

    //admin update password
    public function AdminUpdatePassword(Request $request){

        $validator = Validator::make($request->all(), [
            'old_password'=> 'required',
            'new_password'=> 'required|min:3',
            'confirm_password'=> 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'=> false,
                'errors'=> $validator->errors()
            ]);
        }

        if(Hash::check($request->old_password, Auth::user()->password) == false){

            session()->flash('error','Your old password is incorrect');
            return response()->json([
                'status'=> true,
            ]);
        }


        $user = User::where('id', Auth::user()->id)->where('role', 'admin')->first();
        $user->password = Hash::make($request->new_password);
        $user->save();

        session()->flash('success','Password updated successfully');
        return response()->json([
            'status'=> true,
        ]);

     }


}
