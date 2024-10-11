<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UserController  extends Controller
{
    public function index()
{
  $users=User::orderby('created_at','DESC')->paginate(5);
  return view ('admin.users.list',[
               'users'=>$users
  ]);
  }
   public function edit($id){
   $user=User::findOrfail($id);
   return view('admin.users.edit',[
               'user'=>$user
   ]);
}

public function update($id, Request $request){
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
            session()->flash('success','User Information Updated Successfully');
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
public function destroy(Request $request ){
  $id =$request->id;
  $user=User::findOrFail($id);
  if($user == null){
    session()->flash('error','User not found');
    return response()->json([
      'status'=>false,
    ]);
  }
  $user->delete();
   session()->flash('success','User deleted successfully');
    return response()->json([
      'status'=>true,
    ]);

}
}
