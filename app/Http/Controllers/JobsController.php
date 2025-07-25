<?php

namespace App\Http\Controllers;

use App\Mail\JobNotificationEmail;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Type;
use App\Models\Job;
use App\Models\SavedJob;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class JobsController extends Controller
{
    //find jobs page
    public function index(Request $request){

        $categories = Category::orderBy('id','ASC')->where('status',1)->get();
        $types = Type::orderBy('id','ASC')->where('status',1)->get();

        $jobs = Job::where('status',1);

        // Seaarch using keywords
        if (!empty($request->keywords)) {
            $keywordInput = strtolower($request->keywords);
            $jobs = $jobs->where(function($query) use ($keywordInput) {
                $query->orWhereRaw('LOWER(title) LIKE ?', ['%' . $keywordInput . '%']);
                $query->orWhereRaw('LOWER(keywords) LIKE ?', ['%' . $keywordInput . '%']);
            });
        }

        // Search using location
        if (!empty($request->location)) {
            $locationInput = strtolower($request->location);
            $jobs = $jobs->whereRaw('LOWER(location) LIKE ?', ['%' . $locationInput . '%']);
        }

        //Search using category
        if(!empty($request->category)){
            $jobs = $jobs->where('category_id',$request->category);
        }

        //Search using type
        $typeArray =[];
        if(!empty($request->type)){
            //1,2,3
           $typeArray =  explode(',',$request->type);
           $jobs = $jobs->whereIn('type_id',$typeArray);
        }

        //Search using Experience
        if(!empty($request->experience)){
            $jobs = $jobs->where('experience',$request->experience);
        }


        $jobs = $jobs->with(['type','category']);

        if($request->sort == '0') {
            $jobs =  $jobs->orderBy('created_at','ASC');

        } else {
            $jobs->orderBy('created_at','DESC');
        }


        $jobs =  $jobs->paginate(8);


        return view("front.jobs",
        [
            "categories" => $categories,
            "types" => $types,
            "jobs" => $jobs,
            "typeArray" =>  $typeArray
            ]
        );
    }


    //Job detail page
    public function Detail($slug,$id){

        $job = Job::where([
                    'id' => $id,
                    'status'=> 1
                    ])->with(['type', 'category'])->first();

        if($job == null){
            abort(404);
        }

        // Check if the URL slug matches the stored slug
        if ($job->slug !== $slug) {
            return redirect()->route('job.detail', [
                'slug' => $job->slug,
                'id' => $job->id
            ]);
        }

       $count =0;
       if(Auth::user()){
            $count = SavedJob::where([
                'user_id'=> Auth::user()->id,
                'job_id'=> $id,
            ])->count();
       }


       //fetch applications
       $applications = Application::where('job_id', $id)->with('user')->get();


       return view("front.job-detail", [
            'job' => $job,
            'count'=> $count,
            'applications'=> $applications
        ]);
    }



    //job application
    public function ApplyJob(Request $request){
        $id = $request->id;

        $job = Job::where('id', $id)->first();

        // if job not found in db
        if($job == null ) {
            session()->flash('error','Job does not exist');
            return response()->json([
                'status'=> false,
                'message' => 'Job does not exist'
            ]);
        }

        $user = Auth::user();

        // Check if user has uploaded CV
        if (empty($user->cv)) {
            session()->flash('error', 'Please upload your CV before applying.');
            return response()->json([
                'status' => false,
                'message' => 'Please upload your CV before applying.'
            ]);
        }

        $cvFullPath = public_path('upload/cv/' . $user->cv);

        if (!file_exists($cvFullPath)) {
            session()->flash('error', 'Your uploaded CV file is missing on the server. Please re-upload.');
            return response()->json([
                'status' => false,
                'message' => 'Your uploaded CV file is missing on the server. Please re-upload.'
            ]);
        }


        //You can not apply on your own job
        $employer_id = $job->user_id;

        if ($employer_id == $user->id){

            session()->flash('error','You can not apply on your own job');
            return response()->json([
                'status'=> false,
                'message' => 'You can not apply on your own job'
            ]);
        }



        //You can not apply on a job twice
        $jobApplicationCount = Application::where([
            'user_id' =>  $user->id,
            'job_id'=> $id,
        ])->count();

        if($jobApplicationCount > 0){

            session()->flash('error','You have Already Applied on this Job');

            return response()->json([
                    'status'=> false,
                    'message' => 'You have Already Applied on this Job'
            ]);
        }


        //store application
        $application = new Application();
        $application->job_id = $id;
        $application->user_id = Auth::user()->id;
        $application->employer_id = $employer_id;
        $application->applied_date = now();
        $application->save();



        //send notification email to user and admin or employer
        $employer = User::where('id', $employer_id)->first();

        $mailData = [
            'employer' => $employer,
            'user' => $user,
            'cv'=> $user->cv,
            'job' => $job,
        ];

        //Mail::to($employer->email)->send(new JobNotificationEmail($mailData));
       
        Mail::to(env('JOB_NOTIFICATION_EMAIL_TO'))->send(new JobNotificationEmail($mailData));


        session()->flash('success','You have Successfully Applied');

        return response()->json([
                'status'=> true,
                'message' => 'You have Successfully Applied'
        ]);

    }


    //save job
    public function saveJob(Request $request){

       $id = $request->id;

       $job = Job::find($id);

       if($job == null){
            session()->flash('error','Job not found');
            return response()->json([
                    'status'=> false,
            ]);
        }


       //check if user already saved the job
       $count = SavedJob::where([
            'user_id'=> Auth::user()->id,
            'job_id'=> $id,
        ])->count();

        if($count > 0){
            session()->flash('error','You already saved on this job');
            return response()->json([
                    'status'=> false,
            ]);
        }

        //save job
        $savedJob = new SavedJob();
        $savedJob->job_id = $id;
        $savedJob->user_id = Auth::user()->id;
        $savedJob->save();

        session()->flash('success','You have successfully saved the job.');
        return response()->json([
                'status'=> true,
                'message'=> 'You have successfully saved the job'
        ]);

    }

}
