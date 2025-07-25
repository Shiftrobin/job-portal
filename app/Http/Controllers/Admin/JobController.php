<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Job;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class JobController extends Controller
{
    //Create job post
    public function CreateJob(){

        $categories = Category::orderBy('id','ASC')->where('status',1)->get();
        $types = Type::orderBy('id','ASC')->where('status',1)->get();

        return view('admin.jobs.create', [
             'categories'=> $categories,
             'types'=> $types
         ]);

    }

    //Store Job Post
    public function StoreJob(Request $request){

        $rules = [
            'title'=> 'required|min:5|max:200',
            'category'=> 'required',
            'type'=> 'required',
            // 'vacancy'=> 'required|integer',
            'experience'=> 'required',
            'deadline'=> 'required',
            'location'=> 'required|max:200',
            'company_name'=> 'required',
            'description'=> 'required|min:3',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {

            $job = new Job();
            $job->title = $request->title;
            $job->slug = Str::slug($request->title);
            $job->category_id = $request->category;
            $job->type_id  = $request->type;
            $job->user_id  = Auth::user()->id;
            $job->vacancy = $request->vacancy;
            $job->salary = $request->salary;
            $job->deadline = $request->deadline;
            $job->location = $request->location;
            $job->description = $request->description;
            $job->benefits = $request->benefits;
            $job->reponsibility = $request->reponsibility;
            $job->qualifications = $request->qualifications;
            $job->keywords = $request->keywords;
            $job->experience = $request->experience;
            $job->company_name = $request->company_name;
            $job->company_location = $request->company_location;
            $job->company_website = $request->company_website;
            $job->save();

            session()->flash('success','Job Created Successfully.');

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


    //job list
    public function index(){
       $jobs = Job::orderBy("created_at","DESC")->with('user','applications')->paginate(10);
       return view("admin.jobs.list", ["jobs"=> $jobs]);
    }

    //Edit job
    public function edit($id){
        $job = Job::findOrFail($id);
        // dd($job);
        $categories = Category::orderBy('name','DESC')->get();
        $types = Type::orderBy('name','DESC')->get();

        return view("admin.jobs.edit", [
            "job"=> $job,
            "categories"=> $categories,
            "types"=> $types
        ]);
    }


    //Update job
     public function update(Request $request, $id){

        $rules = [
            'title'=> 'required|min:5|max:200',
            'category'=> 'required',
            'type'=> 'required',
            'experience'=> 'required',
            'deadline'=> 'required',
            'location'=> 'required|max:200',
            'company_name'=> 'required',
            'description'=> 'required|min:3',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {

            $job = Job::find($id);
            $job->title = $request->title;
            // $job->slug = Str::slug($request->title);
            $job->category_id = $request->category;
            $job->type_id  = $request->type;
            $job->vacancy = $request->vacancy;
            $job->salary = $request->salary;
            $job->deadline = $request->deadline;
            $job->location = $request->location;
            $job->description = $request->description;
            $job->benefits = $request->benefits;
            $job->reponsibility = $request->reponsibility;
            $job->qualifications = $request->qualifications;
            $job->keywords = $request->keywords;
            $job->experience = $request->experience;
            $job->company_name = $request->company_name;
            $job->company_location = $request->company_location;
            $job->company_website = $request->company_website;

            $job->status = $request->status;
            $job->isFeatured = (!empty($request->isFeatured)) ? $request->isFeatured : 0;
            $job->save();

            session()->flash('success','Job Updated Successfully.');

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



    //Delete job
    public function destroy(Request $request){
        $id = $request->id;
        $job = Job::find($id);

        if($job == null){
            session()->flash('error','Either job deleted or not found');
            return response()->json([
                'status'=> false
            ]);
        }

        $job->delete();

        session()->flash('success','Job deleted successfully');
        return response()->json([
            'status'=> true
        ]);
    }




}
