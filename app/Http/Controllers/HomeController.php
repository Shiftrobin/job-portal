<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Job;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //Home page
    public function index()
    {
        $categories = Category::where('status',1)->orderBy('name','ASC')->take(8)->get();

        $newCategories = Category::where('status',1)->orderBy('name','ASC')->get();

        $featuredJobs = Job::where('status',1)
                        ->orderBy('created_at','DESC')
                        ->with('type')
                        ->where('isFeatured', 1)
                        ->take(6)
                        ->get();

        $latestJobs = Job::where('status',1)
                        ->orderBy('created_at','DESC')
                        ->with('type')
                        ->take(6)
                        ->get();


        return view('front.home',[
            'categories'=> $categories,
            'featuredJobs'=> $featuredJobs,
            'latestJobs'=> $latestJobs,
            'newCategories'=> $newCategories

        ]);
    }
}
