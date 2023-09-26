<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            "name" => "required",
            "mobile" => "required|unique:users,mobile",
            "email" => "required",
            "password" => "required"
        ]);
        $user = new User();
        $user->name = $request->name;
        $user->mobile = $request->mobile;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        Auth::guard('api')->setUser($user);
        $token = $user->createToken('user_token')->accessToken;
        return response()->json([
            "token" => $token,
            "name"=>Auth::user()->name,

            "status" => "true"
        ]);
    }
    public function login(Request $request)
    {
        $request->validate([
            "mobile" => "required",
        ]);
        $user = User::where('mobile', $request->mobile)->first();
        if ($user) {
            if ($user) {
                Auth::guard('api')->setUser($user);
                $token = $user->createToken('user_token')->accessToken;
            } else {
                return response()->json([
                    "message" => "this mobile number is not registered",
                    "status" => "failed"
                ]);
            }
        } else {
            return response()->json([
                "message" => "user not found",
                "status" => "failed"
            ]);
        }

        return response()->json([
            "token" => $token,
            "name"=>$user->name,
            "status" => "true"
        ]);
    }
    public function users()
    {
        $user=User::get();
        if($user){
            return response()->json([
                "data"=>$user,
                "message"=>"All users list",
                "status"=>"true"
            ],200);
        }else{
            return response()->json([
                "message"=>"No users found",
                "status"=>"failed"
            ],404);
        }
    }
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            
            "message" => "Logout successfully",
            "status" => "success"
        ], 200);
    }

    public function user($id)
    {
        $user = User::find($id); // Use first() to retrieve a single user
    
        if ($user) {
            // $token = $user->createToken('user_token')->accessToken;
            return response()->json([
                // "token"=>$token,
                "user" => $user,
                "status" => "success"











                
            ], 200);
        } else {
            return response()->json([
                "message" => "user not found",
                "status" => "failed"
            ], 422);
        }
    }
    
}
