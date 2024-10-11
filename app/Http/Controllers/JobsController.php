<?php

namespace App\Http\Controllers;

use App\Mail\JobNotificationEmail;
use App\Models\Category;
use App\Models\JobType;
use App\Models\Job;
use App\Models\User;
use App\Models\SavedJob;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;



class JobsController extends Controller
{
    //will show job page
    public function index(Request $request){
        // dd($request->all());
        $categories=Category::where('status',operator: 1)->get();
        $jobtypes=JobType::where('status',operator: 1)->get();

        $jobs=Job::where('status',1);
        //search using keyword
        if(!empty($request->keyword)){
            // $jobs=$jobs->orWhere('title','like','%'.$request->keyword.'%');
            //  $jobs=$jobs->orWhere('keywords','like',value: '%'.$request->keyword.'%');
            $jobs=$jobs->where(function($query)use($request){
                $query->orWhere('title','like','%'.$request->keyword.'%');
                $query->orWhere('keywords','like','%'.$request->keyword.'%');

            });
        }
        //search using location
        if(!empty($request->location)){
         $jobs=$jobs->where('location',$request->location);
           
        }
         //search using category
        if(!empty($request->category)){
         $jobs=$jobs->where('category_id',$request->category);
           
        }
        
         $jobTypeArray = $request->input('job_type', []);

          //search using job type
        if(!empty($request->jobType)){
            // 1,2,3
            $jobTypeArray=explode(',',$request->jobType);   
            $jobs=$jobs->whereIn('job_type_id',$jobTypeArray);

        }
        
         //search using experience
        if(!empty($request->experience)){
         $jobs=$jobs->where('experience',$request->experience);
           
        }
        // dd($jobs->toSql(), $request->all());
        $jobs=$jobs->with(relations: ['jobType','category']);
        if(!empty($request->sort) && $request->sort == '0'){
            $jobs=$jobs->orderBy('created_at','ASC');

        }else{
             $jobs=$jobs->orderBy('created_at','DESC');
        }
       
        
        $jobs=$jobs->paginate(9);
        // dd($jobs);


        return view('front.jobs',[
            'categories'=>$categories,
            'jobtypes'=>$jobtypes,
            'jobs'=>$jobs,
            'jobTypeArray'=>$jobTypeArray
        ])->with('keyword', $request->keyword);

    }
    // show job detail page
    public function detail($id){
        $job=Job::where([
            'id'=>$id , 
            'status'=>1
            ] )->first();

            if($job == null){
                abort(404);
            }
            $count=0;
            if(Auth::user()){
                    $count=SavedJob::where([
                    'user_id'=>Auth::user()->id,
                    'job_id'=>$id,

                     ])->count();
            }
            //fetch applications
            $applications=JobApplication:: where('job_id', $id)->with('user')->get();



        return view('front.jobDetail',[
            'job'=>$job ,
            'count'=>$count ,
            'applications'=>$applications]);
    }
   public function applyJob(Request $request){
        set_time_limit(120); 

    $id = $request->id;

    $job = Job::find($id);
    if (!$job) {
        return response()->json(['status' => false, 'message' => 'Job does not exist']);
    }

    $employer_id = $job->user_id;
    if ($employer_id == Auth::user()->id) {
        return response()->json(['status' => false, 'message' => 'You cannot apply for your own job']);
    }

    $jobApplicationCount = JobApplication::where([
        'user_id' => Auth::user()->id,
        'job_id' => $id,
    ])->count();

    if ($jobApplicationCount > 0) {
        return response()->json(['status' => false, 'message' => 'You have already applied for this job']);
    }

    $application = new JobApplication();
    $application->job_id = $id;
    $application->user_id = Auth::user()->id;
    $application->employer_id = $employer_id;  
    $application->applied_date = now();
    $application->save();

    
    //send notification email to employer
    $employer=User::where('id',$employer_id)->first();
    $mailDate=[
        'employer'=> $employer,
        'user'=>Auth::user(),
        'job'=>$job,
        
    ];
    Mail::to($employer->email)->send(new JobNotificationEmail($mailDate));



    return response()->json([
        'status' => true, 
        'message' => 'You have successfully applied'
    ]);
}
    public function saveJob(Request $request){
        $id =$request->id;
        $job= Job::find($id);

        if($job == null){
            session()->flash('error','Job not found');
             return response()->json([
                                    'status' => false, 
                                    'message' => 'Job not found'
                                   
             ]);
            
        }
        //check if user already saved the job
        $count=SavedJob::where([
            'user_id'=>Auth::user()->id,
        'job_id'=>$id,

        ])->count();

        if($count >0){
             session()->flash('error','You already saved this job');
             return response()->json([
                                    'status' => false, 
                                    'message' => 'You already saved this job'
                                   
             ]);

        }
        $savedJob = new savedJob;
        $savedJob->job_id= $id;
        $savedJob->user_id=Auth::user()->id;
        $savedJob->save();

        session()->flash('success','You have successfully saved this job');
             return response()->json([
                                    'status' => true, 
                                    'message' => 'You have successfully saved this job'
                                   
             ]);

    }

}
