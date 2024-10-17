<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordEmail;
use App\Models\Category;
use App\Models\JobType;
use App\Models\User;
use App\Models\Job;
use App\Models\SavedJob;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    //show user registration page
    public function registration(){
        return view('front.account.registration');
    }

    //will save a user
    public function processRegistration(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|same:confirm_password',
            'confirm_password' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->toArray()
            ]);
        }

        // Create user after validation passes
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        Session()->flash('success', 'You have registered successfully');
        

        return response()->json([
            'status' => true,
            'message' => 'Registration successful'
        ]);
        
    }

    //show user login page
    public function login(){
        return view('front.account.login');
    }
    public function authenticate(Request $request){
        $validator=Validator::make($request->all(),[
            'email'=>'required |email',
            'password'=>'required'

        ]);
        if($validator->passes()){
            if(Auth::attempt(['email'=>$request->email , 'password'=>$request->password])){
                return redirect()->route('account.profile');
            }else{
                return redirect()->route('account.login')->with('error','Invalid email or password');
            }

        }else{
            return redirect()->route('account.login')
            ->withErrors($validator)
            ->withInput($request->only('email'));
        }
    }
    public function profile(){

        $id= Auth::user()->id;
        $user=User::where('id',$id)->first();
         return view('front.account.profile',[
            'user'=>$user,
         ]);
        
    }
    public function updateProfile(Request $request){
        $id= Auth::user()->id;
        $validator=Validator::make($request->all(),[
            'name'=>['required','min:5','max:30'],
            'email' => 'required|email|unique:users,email,' . $id . ',id',

        ]);
         if($validator->passes()){
            $user=User::findOrFail($id);
            $user->name=$request->name;
            $user->email=$request->email;
            $user->designation=$request->designation;
            $user->mobile=$request->mobile;
            $user->save();
            session()->flash('success','Profile Updated Successfully');

             return response()->json([
                'status'=>true,
                'errors'=>[]

            ]);

         }else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()

            ]);
         }

    }

    public function logout(){
        Auth::logout();
        return redirect()->route('account.login');
    }
 public function updateProfilePic(Request $request)
{
    // الحصول على ID المستخدم الحالي
    $id = Auth::user()->id;

    // التحقق من صحة البيانات
    $validator = Validator::make($request->all(), [
        'image' => ['required', 'image'], // التأكد أن الصورة صحيحة
    ]);

    if ($validator->passes()) {
        // تخزين الصورة
        $image = $request->file('image');
        $ext = $image->getClientOriginalExtension(); // الحصول على الامتداد
        $imageName = $id . '-' . time() . '.' . $ext; // إنشاء اسم فريد للصورة
        $image->move(public_path('/profile_pic'), $imageName); // نقل الصورة إلى مجلد "profile_pic"

        // تحديث مسار الصورة في قاعدة البيانات
        User::where('id', $id)->update(['image' => $imageName]);

        // إنشاء الصورة المصغرة (thumbnail)
       $manager = new ImageManager(Driver::class); // إنشاء كائن ImageManager

        // قراءة الصورة الأصلية
        $image = $manager->read(public_path('/profile_pic/' . $imageName));

        // قص الصورة إلى أبعاد 150x150 بكسل
        $image->cover(150, 150);

        // حفظ الصورة المصغرة في مجلد "thumb"
        $thumbPath = public_path('/profile_pic/thumb/' . $imageName);
        $image->save($thumbPath);
        //delete old picture
        File::delete(public_path('/profile_pic/thumb/' . Auth::user()->image));
        File::delete(public_path('/profile_pic/' . Auth::user()->image));


        // عرض رسالة نجاح
       Session()->flash('success', 'Picture Updated Successfully');
        return response()->json([
            'status' => true,
            'errors' => []
        ]);
    } else {
        // إعادة الأخطاء إذا لم تمر البيانات
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ]);
    }
}
public function createJob(){
    $categories=Category::orderBy('name','ASC')->where('status',1)->get();
    $jobTypes=JobType::orderBy('name','ASC')->where('status',1)->get();
    return view('front.account.job.create',[
        'categories'=> $categories,
        'jobTypes'=>$jobTypes,
       
    ]);
     if(Auth::user() == ""){
            return view('front.account.login');
        }


}
public function saveJob(Request $request){
    
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
        'qualifications'=>['required','min:10'],

    ];
    $validator=Validator::make($request->all(),$rules);
    if($validator->passes()){
        $job=new Job();
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
        $job->qualifications=$request->qualifications;
        $job->keywords=$request->keywords;
        $job->experience=$request->experience;
        $job->company_name=$request->company_name;
        $job->company_location=$request->company_location;
        $job->company_website=$request->company_website;
        $job->save();
        Session()->flash('success','Job Added Successfully');
        return response()->json([
            'status'=>true,
            'errors'=>[],
            
        ]);
    }else{
        return response()->json([
            'status'=>false,
            'errors'=>$validator->errors(),
        ]);
    }
}
public function myJob(){
    $jobs=Job::where('user_id',Auth::user()->id)->with('jobType')->orderBy('created_at','DESC')->paginate(5);
    // dd($jobs);

    return view('front.account.job.my-jobs',[
        'jobs'=>$jobs
    ]);

}
   public function editJob(Request $request,$id){
    // dd($id);
    $categories=Category::orderBy('name','ASC')->where('status',1)->get();
    $jobTypes=JobType::orderBy('name','ASC')->where('status',1)->get();
    $job=Job::where([
        'user_id'=>Auth::user()->id,
        'id'=>$id
    ])->first();
    if($job==null){
        abort(404);

    }
    return view('front.account.job.edit',[
        'categories'=>$categories,
        'jobTypes'=>$jobTypes,
        'job'=>$job,
    ]);

   }
   public function updateJob(Request $request,$id){

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
        'qualifications' => 'required|string'

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
        $job->qualifications=$request->qualifications;
        $job->keywords=$request->keywords;
        $job->experience=$request->experience;
        $job->company_name=$request->company_name;
        $job->company_location=$request->company_location;
        $job->company_website=$request->company_website;
        $job->save();
        Session()->flash('success','Job Updated Successfully');
        return response()->json([
            'status'=>true,
            'errors'=>[],
        ]);
    }else{
        return response()->json([
            'status'=>false,
            'errors'=>$validator->errors(),
        ]);
    }
}
public function deleteJob(Request $request)
{
    // البحث عن الوظيفة المرتبطة بالمستخدم الحالي والـ ID المرسل
    $job = Job::where([
        'user_id' => Auth::user()->id,
        'id' => $request->jobId
    ])->first();

    // إذا كانت الوظيفة غير موجودة
    if ($job == null) {
        session()->flash('error', 'Either job deleted or not found');
        return response()->json([
            'status' => false,
        ]);
    }

    // حذف الوظيفة
   Job::where('id', $request->jobId)->delete();


    // تأكيد نجاح العملية
    session()->flash('success', 'Job Deleted Successfully');
    return response()->json([
        'status' => true,
    ]);
}
public function myJobApplications(){
    $jobApplications=JobApplication::where('user_id',Auth::user()->id)
    ->with('job','job.jobType','job.applications')->get();
    return view('front.account.job.my-job-application',[
        'jobApplications'=>$jobApplications,
        
    ]);
}

public function removeJobs(Request $request) {
    $jobApplication = JobApplication::where([
        'id' => $request->id,
        'user_id' => Auth::user()->id
    ])->first();

    if ($jobApplication == null) {
        session()->flash('error', 'Job application not found');
        return response()->json(['status' => false]);
    }

    $jobApplication->delete();  // حذف السجل
    session()->flash('success', 'Job application removed successfully');
    return response()->json(['status' => true]);
}

public function savedJobs(){
     $savedJobs=SavedJob::where([
        'user_id'=>Auth::user()->id,
     ])->with('job','job.jobType','job.applications')
     ->orderBy('created_at','DESC')
     ->paginate(10);
    return view('front.account.job.saved-jobs',[
        'savedJobs'=>$savedJobs,
        
    ]);
}
   public function removeSavedJobs(Request $request) {
    $savedJob = SavedJob::where([
        'id' => $request->id,
        'user_id' => Auth::user()->id
    ])->first();

    if ($savedJob == null) {
        session()->flash('error', 'Job  not found');
        return response()->json(['status' => false]);
    }

    $savedJob->delete();  // حذف السجل
    session()->flash('success', 'Job removed successfully');
    return response()->json([
        'status' => true]);
}
public function updatePassword(Request $request) {
    $validator = Validator::make($request->all(), [
        'old_password' => 'required',
        'new_password' => 'required|min:8',
        'confirm_password' => 'required|same:new_password',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors(),
        ]);
    }
    if(Hash::check($request->old_password,Auth::user()->password) == false){
        session()->flash('error','Your old password is incorrect');
        return response()->json([
            'status' => true,
        ]);
    }
    $user=User::find(Auth::user()->id);
    $user->password = Hash::make($request->new_password);
    $user->save();
    session()->flash('success','Password Changed Successfully');
        return response()->json([
            'status' => true,
        ]);


}
   public function forgotPassword(){
    return view('front.account.forgot-password');
   }
   public function processForgotPassword(Request $request){
    set_time_limit(120); 
     $validator = Validator::make($request->all(), [
        'email' => ['required','email','exists:users,email'],
        

    ]);
      if($validator->fails())
      {
        return redirect()->route('account.forgotPassword')->withInput()
        ->withErrors($validator);
      }
      $token=Str::random(10);
      \DB::table('password_reset_tokens')->where('email',$request->email)->delete();
       \DB::table('password_reset_tokens')->insert([
        'email'=>$request->email,
        'token'=>$token,
        'created_at'=> now()
       ]);
       //send email here
       $user=User::where('email',$request->email)->first();
       $mailData=[
        'token'=>$token,
        'user'=>$user,
        'subject'=>'You have repuested to change your password',
       ];
       Mail::to($request->email)->send(new ResetPasswordEmail($mailData));
       return redirect()->route('account.forgotPassword')->with('success','Reste password email has been sent to your inbox');
       
      }
      public function resetPassword(){
     
        return view('front.account.reset-password',[
         
        ]);

      }  
 public function processResetPassword(Request $request)
{
    // التحقق من صحة البيانات المدخلة
    $validator = Validator::make($request->all(), [
        'new_password' => ['required', 'min:8'],
        'confirm_password' => ['required', 'same:new_password'],
    ]);

    // في حال فشل التحقق، يتم إعادة التوجيه مع الأخطاء
    if ($validator->fails()) {
        return redirect()->route('account.resetPassword')->withErrors($validator);
    }

    // البحث عن المستخدم بواسطة البريد الإلكتروني
    $user = User::where('email', $request->email)->first();

    // التحقق من وجود المستخدم
    if (!$user) {
        // إذا لم يتم العثور على المستخدم، أعد التوجيه مع رسالة خطأ
        return redirect()->route('account.resetPassword')->withErrors(['email' => 'User not found.']);
    }

    // تحديث كلمة المرور
    $user->update([
        'password' => Hash::make($request->new_password)
    ]);

    // إعادة التوجيه إلى صفحة تسجيل الدخول مع رسالة نجاح
    return redirect()->route('account.login')->with('success', 'You have successfully changed your password.');
}

}