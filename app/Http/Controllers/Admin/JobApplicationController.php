<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    //job application list
    public function index(){
       $applications = Application::orderBy("created_at","desc")
                        ->with('job','user','employer')
                        ->paginate(10);

        return view("admin.job-applications.list", [
            "applications"=> $applications
        ]);
    }

    //job application detail
    public function JobApplicationDetail($id){
        $application = Application::find($id);
        return view("admin.job-applications.detail", [
            "application" => $application
        ]);
    }

    //delete job application
    public function destroy (Request $request){
        $id = $request->id;
        $application = Application::find($id);

        if($application == null){
            session()->flash("error","Either job application deleted or not found");
            return response()->json([
                'status'=> false
            ]);
        }

        $application->delete();

        session()->flash('success','Job application deleted successfully');
        return response()->json([
            'status'=> true
        ]);

    }
}
