<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function _construct(){
        this->middleware('auth:api',['except'=>['login','register']]);

    }
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|string|email|unique::users',
            'password' => 'required|string|confirmed|min:6'

        ]);
        // if($validator->false()){
        //    return response()->jason($validator->errors()->tojson(),400);
        // }
        // $user = User::create(array_merge(
        //     $validator -> validated(),
        //     ['password'=>bcrypt($request->password)]
        // ));
        $user=new User();
        $user->name=$request->name;
        $user->email=$request->email;
        $user->password=bcrypt($request->password);
        $user->save();
        return response()->json([
           'message'=>'user successfully registered',
           'user'=>$user

        ],201);
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|string|min:6'

        ]);
        // if($validator->false()){
        //    return response()->jason($validator->errors()->tojson(),422);
        // }
        if(!$token=auth()->attempt($validator->validated())){
            return responce()->json('error','unauthorised',401);
        }
        return $this->createNewToken($token);
    }
    public function createNewToken($token){
        return response()->json([
        'acces_token'=>$token,
        'token_type'=>'bearer',
        'experience_in'=>auth()->factory()->getTTL()*60,
        'user'=>auth()->user()
        ]);

    }
    public function profile(){
        return response()->json(auth()->user());
    }
    public function logout(){
        auth()-> logout();
        return response()->json([
            'message'=>'user logged out',
 
         ]);

    }
}
