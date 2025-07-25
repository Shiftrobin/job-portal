<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordEmail;
use App\Models\Application;
use App\Models\Category;
use App\Models\Job;
use App\Models\SavedJob;
use App\Models\Type;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class AccountController extends Controller
{
    //user registration page
    public function Registration()
    {
        return view('front.account.registration');
    }

    //save user
    public function ProcessRegistration(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email'=> 'required|email|unique:users,email',
            'password'=> 'required|min:3|same:confirm_password',
            'confirm_password' => 'required',
            'g-recaptcha-response' => 'required|captcha'
        ]);

        if($validator->passes()){

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            session()->flash('success', 'You have registered successfully.');

            return response()->json([
                'status'=> true,
                'errors'=> []
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

    }

    //user login
    public function AccountLogin(){
        return view('front.account.login');
    }

    //account authenticate
    public function AccountAuthenticate(Request $request){

        // Validate
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('account.login')
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }

        // Attempt login (email and password only)
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            // Check role
            if (Auth::user()->role === 'user') {
                return redirect()->route('account.dashboard');
            } else {
                Auth::logout();
                return redirect()->route('account.login')->with('error', 'Access denied. Users only.');
            }
        }

        // Login failed
        return redirect()->route('account.login')->with('error', 'Invalid email or password.');
       
    }

    //account dashboard
    public function AccountDashbord(){
      $id = Auth::user()->id;
      $user = User::where('id', $id)->where('role','user')->first();
      $applicationCount = Application::where('user_id', $user->id)->count();
      $jobSavedCount = SavedJob::where('user_id', $user->id)->count();

      return view('front.account.dashboard', [
            'user'=> $user,
            'applicationCount'=> $applicationCount,
            'jobSavedCount'=> $jobSavedCount
        ]);
    }

    //account profile
    public function AccountProfile(Request $request){

      $id = Auth::user()->id;

      $user = User::where('id', $id)->first();

      return view('front.account.profile', ['user'=> $user]);
    }


    //Update profile
    public function UpdateProfile(Request $request){

        $id = Auth::user()->id;

        $vlidator = Validator::make($request->all(),[
            'name'=> 'required|min:5|max:20',
            'email'=> 'required|email|unique:users,email,'.$id.',id',
        ]);

        if ($vlidator->passes()){

            $user = User::find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->address = $request->address;
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


    //account logout
    public function AccountLogout(Request $request){
        Auth::logout();
        return redirect()->route('account.login');
    }

    //update profile picture
    public function UpdateProfilePic(Request $request){

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


    // My CV
    public function MyCV(Request $request){
        $user = Auth::user();
        $cv = $user->cv;
        return view('front.account.cv.my-cv', ['cv'=> $cv]);
    }


    // CV Store
    public function CVStore(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'cv' => 'required|mimes:pdf|max:5120', // PDF only, max 5MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        // Store new CV
        $cv = $request->file('cv');
        $cvName = $user->id . '-' . time() . '.' . $cv->getClientOriginalExtension();
        $cv->move(public_path('/upload/cv/'), $cvName);

        // Delete old CV if exists
        if ($user->cv) {
            File::delete(public_path('/upload/cv/' . $user->cv));
        }

        // Update user
        $user->update([
            'cv' => $cvName,
        ]);

        session()->flash('success', 'CV updated successfully');

        return response()->json([
            'status' => true,
            'errors' => []
        ]);
    }

    // Cover Letter
    public function CoverLetter(){
        $user = Auth::user();
        $cover_letter = $user->cover_letter;
        return view('front.account.cover_letter.cover_letter', ['cover_letter' => $cover_letter]);
    }

    //Update Cover Letter
     public function CoverLetterUpdate(Request $request){

        $user = Auth::user();

        $rules = [
            'cover_letter'=> 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {

            $user->cover_letter = $request->cover_letter;
            $user->save();

            session()->flash('success','Cover Letter Updated Successfully.');

            return response()->json([
                'status'=> true,
                'errors'=>[]
            ]);
        } else {
            return response()->json([
                'status'=> false,
                'errors'=> $validator->errors()
            ]);
        }
    }



    //jobs applied
    public function myJobApplication(){

        $jobApplications =   Application::where('user_id', Auth::user()->id)
                            ->with(['job', 'job.type','job.applications'])
                            ->orderBy('created_at','DESC')
                            ->paginate(10);
        // dd(jobApplications);

        return view('front.account.job.my-job-applications', [
            'jobApplications'=> $jobApplications
        ]);
    }

    //Remove Job application
    public function removeJob(Request $request){

       $jobApplication = Application::where([
            'id' => $request->id,
            'user_id' => Auth::user()->id
        ])->first();

        if ($jobApplication == null){
            session()->flash('error','Job Application not found');
            return response()->json([
                'status'=> false,
            ]);
        }

        //remove job application
        $jobApplication::find($request->id)->delete();

        session()->flash('success','Job application removed successfully.');
        return response()->json([
            'status'=> true,
        ]);

    }

    //saved jobs
    public function savedJobs(){

        // $jobApplications =   Application::where('user_id', Auth::user()->id)
        // ->with('job', 'job.type','job.applications')
        // ->paginate(10);
        // dd($jobs);

        $savedJobs = SavedJob::where(['user_id' => Auth::user()->id])
            ->with(['job', 'job.type','job.applications'])
            ->orderBy('created_at','DESC')
            ->paginate(10);

        return view('front.account.job.saved-jobs', [
            'savedJobs'=> $savedJobs
        ]);

    }


    //Remove saved jobs
    public function removeSavedJob(Request $request){

        $savedJob = SavedJob::where([
            'id' => $request->id,
            'user_id' => Auth::user()->id
        ])->first();

        if ($savedJob == null){
            session()->flash('error','Job not found');
            return response()->json([
                'status'=> false,
            ]);
        }

        //remove job application
        $savedJob::find($request->id)->delete();

        session()->flash('success','Saved job removed successfully.');
        return response()->json([
            'status'=> true,
        ]);

     }



     //update password
     public function UpdatePassword(Request $request){

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


        $user = User::find(Auth::user()->id);
        $user->password = Hash::make($request->new_password);
        $user->save();

        session()->flash('success','Password updated successfully');
        return response()->json([
            'status'=> true,
        ]);

     }


     //forgot password
     public function forgotPassword(){
        return view('front.account.forgot-password');
     }

     //process forget password
     public function processForgotPassword(Request $request) {
        $validator = Validator::make(request()->all(), [
            'email'=> 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return redirect()->route('account.forgot.password')->withInput()->withErrors($validator);
        }

        $token = Str::random(10);

        DB::table('password_reset_tokens')->where('email',$request->email)->delete();

        DB::table('password_reset_tokens')->insert([
            'email'=> $request->email,
            'token' => $token,
            'created_at' => now()
        ]);


        //send email
        $user = User::where('email',$request->email)->first();
        $mailData = [
           'token' => $token,
           'user'=> $user,
           'subject'=> 'You have requested to change your password'
        ];

        Mail::to($request->email)->send(new ResetPasswordEmail($mailData));

        return redirect()->route('account.forgot.password')->with('success','Reset password email has been sent.Please check your email inbox');


     }

     //reset password
     public function resetPassword($tokenString) {
        // echo''.$token.'';

       $token = DB::table('password_reset_tokens')->where('token',$tokenString)->first();

       if($token == null) {
         return redirect()->route('account.forgot.password')->with('error','Invalid token');
       }

       return view('front.account.reset-password',[
        'tokenString'=> $tokenString
    ]);
     }


     //process reset password
     public function processResetPassword(Request $request) {

        $token = DB::table('password_reset_tokens')->where('token',$request->token)->first();

        if($token == null) {
            return redirect()->route('account.forgot.password')->with('error','Invalid token');
        }


        $validator = Validator::make(request()->all(), [
            'new_password'=> 'required|min:3',
            'confirm_password'=> 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return redirect()->route('account.forgot.password', $request->token)->withInput()->withErrors($validator);
        }

        User::where('email',$token->email)->update([
            'password'=> Hash::make($request->new_password)
        ]);

        return redirect()->route('account.login')->with('success','You have successfully changed you password.');
     }

}
