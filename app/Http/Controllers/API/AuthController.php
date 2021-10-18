<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|max:191',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validation_err' => $validator->getMessageBag()
            ]);
        } else {
            $user = Users::where('username', $request->username)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Invalid Credentials'
                ]);
            } else {
                if($user->role_as == 1) //1==admin
                {
                    $role='admin';
                    $token = $user->createToken($user->username.'_AdminToken' , ['server:admin'])->plainTextToken;
                }else{
                    $role='';
                    $token = $user->createToken($user->username . '_Token', [''])->plainTextToken;
                }
                
                return response()->json([
                    'status' => 200,
                    'username' => $user->username,
                    'token' => $token,
                    'message' => "Logged on Successfully",
                    'role' => $role
                ]);
            }
        }
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|max:191|unique:users,username',
            'password' => 'required|min:6',
            'fullname' => 'required|max:191',
            'email' => 'required|email|max:191',
            'phone' => 'required|max:10|min:10|unique:users,phone',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validation_err' => $validator->getMessageBag()
            ]);
        } else {
            $user = Users::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'fullname' => $request->fullname,
                'email' => $request->email,
                'phone' => $request->phone
            ]);

            $token = $user->createToken($user->username . '_Token')->plainTextToken;

            return response()->json([
                'status' => 200,
                'username' => $user->username,
                'token' => $token,
                'message' => "You can login now"
            ]);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Logged Out Successfully'
        ]);
    }
}
