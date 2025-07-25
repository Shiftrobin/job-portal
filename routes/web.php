<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JobApplicationController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteMapController;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


//cache clear
Route::get('/cc', function(){
	try {
        Artisan::call('config:clear');
		Artisan::call('view:clear');
        Artisan::call('route:clear');
		Artisan::call('cache:clear');
		Artisan::call('config:cache');
		Artisan::call('route:cache');
		Artisan::call('view:cache');
	    return "Cache Cleared!";
	} catch(\Exception $e) {
		dd($e);
	}
});


//Public routes
Route::get('/', [HomeController::class, 'index'])->name('home')->middleware('pagespeed');
Route::get('/jobs', [JobsController::class, 'index'])->name('jobs')->middleware('pagespeed');
Route::get('/job/{slug}/{id}',[JobsController::class,'Detail'])->name('job.detail');
Route::post('/apply-job', [JobsController::class, 'ApplyJob'])->name('apply.job');
Route::post('/save-job', [JobsController::class, 'saveJob'])->name('save.job');
//site map
Route::get('/sitemap',[SiteMapController::class,'index'])->name('sitemap');


//Admin Routes
//private
Route::group(['prefix' => 'admin','middleware' => ['auth', 'check.admin']], function () {

    //profile routes
    Route::get('/dashboard', [DashboardController::class,'index'])->name('admin.dashboard');
    Route::get('/profile', [DashboardController::class,'AdminProfile'])->name('admin.profile');
    Route::put('/update-profile', [DashboardController::class,'AdminUpdateProfile'])->name('admin.update.profile');
    Route::post('/update-password', [DashboardController::class,'AdminUpdatePassword'])->name('admin.update.password');

    Route::post('/update-profile-pic', [DashboardController::class,'AdminUpdateProfilePic'])->name('admin.update.profile.pic');

    Route::get('/logout', [DashboardController::class,'AdminLogout'])->name('admin.logout');

    //users routes
    Route::get('/users', [UserController::class,'index'])->name('admin.users.list');
    Route::get('/user/edit/{id}', [UserController::class,'edit'])->name('admin.user.edit');
    Route::put('/user/update/{id}', [UserController::class,'update'])->name('admin.user.update');
    Route::delete('/user/delete', [UserController::class,'destroy'])->name('admin.user.destroy');

    //jobs routes
    Route::get('/jobs', [JobController::class,'index'])->name('admin.jobs.list');
    Route::get('/create-job', [JobController::class,'CreateJob'])->name('admin.create.job');
    Route::post('/store-job', [JobController::class,'StoreJob'])->name('admin.store.job');

    Route::get('/job/edit/{id}', [JobController::class,'edit'])->name('admin.job.edit');
    Route::put('/job/update/{id}', [JobController::class,'update'])->name('admin.job.update');
    Route::delete('/job/delete', [JobController::class,'destroy'])->name('admin.job.destroy');

    //job applications routes
    Route::get('/job-applications', [JobApplicationController::class,'index'])->name('admin.job.applications.list');
    Route::get('/job-application-detail/{id}', [JobApplicationController::class,'JobApplicationDetail'])->name('admin.job.applications.detail');
    Route::delete('/job-application-delete', [JobApplicationController::class,'destroy'])->name('admin.job.application.destroy');
});

//public admin routes
Route::get('/multi/middleware', [DashboardController::class,'AdminLogin'])->name('admin.login');
Route::post('/admin/authenticate', [DashboardController::class,'AdminAuthenticate'])->name('admin.authenticate');


Route::group(['prefix' => 'account'], function(){

    //Guest Route
    Route::group(['middleware'=> 'guest'], function(){
        Route::get('/register', [AccountController::class, 'Registration'])->name('account.registration');
        Route::post('/process-register', [AccountController::class,'ProcessRegistration'])->name('account.process.registration');;
        Route::get('/login', [AccountController::class,'AccountLogin'])->name('account.login')->middleware('pagespeed');
        Route::post('/authenticate', [AccountController::class,'AccountAuthenticate'])->name('account.authenticate');
    });

    //Authenticated Route
    Route::group(['middleware'=> ['auth', 'check.user'] ], function(){
        //profile routes
        Route::get('/dashboard', [AccountController::class,'AccountDashbord'])->name('account.dashboard');
        Route::get('/logout', [AccountController::class,'AccountLogout'])->name('account.logout');

        Route::get('/profile', [AccountController::class,'AccountProfile'])->name('account.profile');
        Route::put('/update-profile', [AccountController::class,'UpdateProfile'])->name('account.update.profile');
        Route::post('/update-password', [AccountController::class,'UpdatePassword'])->name('account.update.password');
        Route::post('/update-profile-pic', [AccountController::class,'UpdateProfilePic'])->name('update.profile.pic');

        //cv routes
        Route::get('/my-cv', [AccountController::class,'MyCV'])->name('account.my.cv');
        Route::post('/cv-store', [AccountController::class,'CVStore'])->name('account.cv.store');

        //cover letter routes
        Route::get('/cover-letter', [AccountController::class,'CoverLetter'])->name('account.cover.letter');
        Route::post('/cover-letter-update', [AccountController::class,'CoverLetterUpdate'])->name('account.cover.letter.update');

        //job routes
        // Route::get('/create-job', [AccountController::class,'CreateJob'])->name('account.create.job');
        // Route::post('/save-job', [AccountController::class,'SaveJob'])->name('account.save.job');
        // Route::get('/my-jobs', [AccountController::class,'MyJobs'])->name('account.my.jobs');
        // Route::get('/my-jobs/edit/{jobId}', [AccountController::class,'EditJob'])->name('account.edit.job');
        // Route::post('/update-job/{id}', [AccountController::class,'UpdateJob'])->name('account.update.job');
        // Route::post('/delete-job', [AccountController::class,'DeleteJob'])->name('account.delete.job');

        //job application routes
        Route::get('/my-job-applications', [AccountController::class,'myJobApplication'])->name('account.my.job.applications');
        Route::post('/remove-job-application', [AccountController::class,'removeJob'])->name('account.remove.job.application');

        //saved jobs or wishlist
        Route::get('/saved/jobs', [AccountController::class,'savedJobs'])->name('account.saved.jobs');
        Route::post('/remove-saved-job', [AccountController::class,'removeSavedJob'])->name('account.remove.saved.job');
    });

});


//public user route
Route::get('/forgot-password', [AccountController::class,'forgotPassword'])->name('account.forgot.password');
Route::post('/process-forgot-password', [AccountController::class,'processForgotPassword'])->name('account.process.forgot.password');
Route::get('/reset-password/{token}', [AccountController::class,'resetPassword'])->name('account.reset.password');
Route::post('/process-reset-password', [AccountController::class,'processResetPassword'])->name('account.process.reset.password');
