<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
   public function register(Request $request)
   {
            $fields=$request->validate([
                'name'=>'required|string',
                'email'=>'email|required|string|unique:users,email,',
                'password'=>'required|confirmed|string'
            ]);
            $user=User::create([
               'name'=>$fields['name'],
               'email'=>$fields['email'],
               'password'=>bcrypt($fields['password'])
            ]);
            $token=$user->createToken('myapptoken')->plainTextToken;
            $response=[
                'user'=>$user,
                'token'=>$token
            ];
            return response($response,201);
   }

   public function login(Request $request)
   {
            $fields=$request->validate([
                'email'=>'email|required|string',
                'password'=>'required|string'
            ]);
            //check email
            $user=User::where('email',$fields['email'])->first();
            //check pass
            if (!$user||!Hash::check($fields['password'], $user->password)) {
                return response([
                    'message'=>'Bad creds'
                ],401);
            }

            $token=$user->createToken('myapptoken')->plainTextToken;
            $response=[
                'user'=>$user,
                'token'=>$token
            ];
            return response($response,201);
   }

   public function logout(Request $request)
   {
       if ($request->user()) {
        $request->user()->tokens()->delete();

       }
    //auth()->user()->tokens()->delete();//yeh bhi  chal rha hai
    //auth('sanctum')->user()->tokens()->delete();//error nhi hai yeh bhi chal rha hai
       return [
            'message'=> 'logged out'
       ];
   }
}
