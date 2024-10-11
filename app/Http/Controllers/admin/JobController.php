<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Job;
use App\Models\JobType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    public function index(){
        $jobs=Job::orderBy('created_at','DESC')->with('user','applications')->paginate(10);        return view('admin.jobs.list',[
            'jobs'=>$jobs,

        ]);
    }
    public function edit($id){
        $job= Job::findOrFail($id);
        $categories= Category::orderBy('name','ASC')->get();
        $jobTypes= JobType::orderBy('name','ASC')->get();
        return view('admin.jobs.edit',[
            'job'=>$job,
            'categories'=>$categories,
            'jobTypes'=>$jobTypes,
        ] );
    }
       public function update(Request $request,$id){

    $rules=[
        'title'=>['required','min:6','max:200'],
        'category'=>['required'],
        'jobType'=>['required'],
        'vacancy'=>['required','integer'],
        'location'=>['required'],
        'description'=>['required'],
        'benefits'=>['required','string'],
        'responsabilites'=>['required','string'],
        'company_name'=>['required','min:3','max:70'],
        'qualification' => 'required|string'

    ];
    $validator=Validator::make($request->all(),$rules);
    if($validator->passes()){
        
        $job=Job::findOrFail($id);
        $job->title=$request->title;
        $job->category_id=$request->category;
        $job->job_type_id=$request->jobType;
        $job->user_id=Auth::user()->id;
        $job->vacancy=$request->vacancy;
        $job->salary=$request->salary;
        $job->location=$request->location;
        $job->description=$request->description;
        $job->benefits=$request->benefits;
        $job->responsabilites=$request->responsabilites;
        $job->qualification =$request->qualification;
        $job->keywords=$request->keywords;
        $job->experience=$request->experience;
        $job->company_name=$request->company_name;
        $job->company_location=$request->company_location;
        $job->company_website=$request->company_website;
        $job->status=$request->status;
        $job->isFeatured=(!empty($request->isFeatured)) ?$request->isFeatured : 0; 
        $job->save();
        Session()->flash('success','Job Updated Successfully');
        return response()->json([
            'status'=>true,
        ]);
    }else{
        return response()->json([
            'status'=>false,
            'errors'=>$validator->errors(),
        ]);
    }
}
    public function destroy(Request $request){
        $id= $request->id;
        $job=Job::findOrFail($id);
        if($job == null){
            session()->flash('error' ,'Either job deleted or not found');
            return response()->json([
            'status'=>false,
        ]);
        }
        $job->delete();
         session()->flash('success' ,'Job deleted successfully');
            return response()->json([
            'status'=>true,
        ]);
    }
}
