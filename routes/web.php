<?php
use App\Http\Controllers\AccountController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\admin\JobController;
use App\Http\Controllers\admin\JobApplicationController;
use Illuminate\Support\Facades\Route;
// Route::get('/', function () {
//     return view('welcome');
// });
Route::prefix('user')->group(function(){
    Route::get('/home',action: [HomeController::class,'index'])->name('user.home');

}) ;


Route::get('/jobs',action: [JobsController::class,'index'])->name('jobs');
Route::get('/jobs/detail/{id}',action: [JobsController::class,'detail'])->name('jobDetail');
Route::post('/apply-job',action: [JobsController::class,'applyJob'])->name('applyJob');
Route::post('/save-job',action: [JobsController::class,'saveJob'])->name('saveJob');
Route::get('/forgot-password',action: [AccountController::class,'forgotPassword'])->name('account.forgotPassword');
Route::post('/process-forgot-password',action: [AccountController::class,'processForgotPassword'])->name('account.processForgotPassword');
Route::get('/reset-password/{token}',action: [AccountController::class,'resetPassword'])->name('account.resetPassword');
Route::get('/process-reset-password',action: [AccountController::class,'processResetPassword'])->name('account.processResetPassword');


Route::group(['prefix' => 'admin', 'middleware' => 'checkadmin'], function () {
    Route::get('/dashboard', action: [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', action: [UserController::class, 'index'])->name('admin.users');
    Route::get('/users/{id}', action: [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{id}', action: [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users', action: [UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/jobs', action: [JobController::class, 'index'])->name('admin.jobs');
    Route::get('/jobs/edit/{id}', action: [JobController::class, 'edit'])->name('admin.jobs.edit');
    Route::put('/jobs/{id}', action: [JobController::class, 'update'])->name('admin.jobs.update');
    Route::delete('/jobs', action: [JobController::class, 'destroy'])->name('admin.jobs.destroy');
    Route::get('/job-applications', action: [JobApplicationController::class, 'index'])->name('admin.jobApplications');
    Route::delete('/job-applications', action: [JobApplicationController::class, 'destroy'])->name('admin.jobApplications.destroy');

});


Route::group(['prefix'=>'account'],function(){
    //guest
    Route::group(['middleware'=>'guest'],function(){
        Route::get('/register', [AccountController::class,'registration'])->name('account.registration');
        Route::post('/process_register', [AccountController::class,'processRegistration'])->name('account.processRegistration');
        Route::get('/login', [AccountController::class,'login'])->name('account.login');
        Route::post('/authenticate', [AccountController::class, 'authenticate'])->name('account.authenticate');

    });



    //Auth
    Route::group(['middleware'=>'auth'],function(){
        Route::get('/profile', [AccountController::class, 'profile'])->name('account.profile');
        Route::put('/update-profile', [AccountController::class, 'updateProfile'])->name('account.updateProfile');
        Route::get('/logout', [AccountController::class, 'logout'])->name('account.logout');
        Route::post('/update-profile-pic', [AccountController::class, 'updateProfilePic'])->name('account.updateProfilePic');
        Route::get('/create-job', [AccountController::class, 'createJob'])->name('account.createJob');
        Route::post('/save-job', [AccountController::class, 'saveJob'])->name('account.saveJob');
        Route::get('/my-job', [AccountController::class, 'myJob'])->name('account.myJob');
        Route::get('/my-job/edit/{jobId}', [AccountController::class, 'editJob'])->name('account.editJob');
        Route::get('/update-job/{jobId}', [AccountController::class, 'updateJob'])->name('account.updateJob');
        Route::post('/delete-job', [AccountController::class, 'deleteJob'])->name('account.deleteJob');
        Route::get('/my-job-application', [AccountController::class, 'myJobApplications'])->name('account.myJobApplications');
        Route::post('/remove-job-application', [AccountController::class, 'removeJobs'])->name('account.removeJobs');
        Route::get('/saved-job', [AccountController::class, 'savedJobs'])->name('account.savedJobs');
        Route::post('/change', [AccountController::class, 'updatePassword'])->name('account.updatePassword');    
        Route::post('/remove-saved-job-application', [AccountController::class, 'removeSavedJobs'])->name('account.removeSavedJobs');



    });
});