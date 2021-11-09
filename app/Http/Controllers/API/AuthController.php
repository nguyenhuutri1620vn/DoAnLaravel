<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'username' => 'required|max:191',
                'password' => 'required'
            ],
            [
                'username.required' => "Vui lòng nhập tên đăng nhặp",
                'username.max' => "Tên đăng nhập không hợp lệ",
                'password.required' => 'Vui lòng nhập mật khẩu',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'validation_err' => $validator->getMessageBag()
            ]);
        } else {
            $user = Users::where('username', $request->username)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Mật khẩu hoặc tài khoản không đúng'
                ]);
            } else {
                if ($user->role_as == 2) //1==admin
                {
                    $role = 'admin';
                    $token = $user->createToken($user->username . '_AdminToken', ['server:admin'])->plainTextToken;
                } else if ($user->role_as == 1) //1== staff
                {
                    $role = 'staff';
                    $token = $user->createToken($user->username . '_AdminToken', ['server:staff'])->plainTextToken;
                }else{
                    $role = '';
                    $token = $user->createToken($user->username . '_Token', [''])->plainTextToken;
                }

                return response()->json([
                    'status' => 200,
                    'username' => $user->username,
                    'token' => $token,
                    'message' => "Đăng nhập thành công",
                    'role' => $role
                ]);
            }
        }
    }


    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'username' => 'required|max:191|unique:users,username',
                'password' => 'required|min:6',
                'fullname' => 'required|max:191',
                'email' => 'required|email|max:191',
                'phone' => 'required|max:10|min:10|unique:users,phone',
                'passwordConfirm' => 'required|required_with:password|same:password',
            ],
            [
                'username.required' => 'Vui lòng nhập tên đăng nhập',
                'username.max' => "Tên đăng nhập không được quá 191 ký tự",
                'username.unique' => "Tên đăng nhập đã được đăng ký",
                "password.required" => "Vui lòng nhập mật khẩu",
                "password.min" => "'Mật khẩu phải nhiều hơn 6 ký tự",
                "fullname.required" => "Vui lòng nhập họ và tên",
                "fullname.max" => "Họ và tên quá dài",
                "email.required" => "Vui lòng nhập email",
                "email.email" => "Email không hợp lệ",
                "email.max" => "Email không hợp lệ",
                "phone.required" => "Vui lòng nhập số điện thoại",
                "phone.max" => "Số điện thoại không hợp lệ",
                "phone.min" => "Số điện thoại không hợp lệ",
                "phone.unique" => "Số điện thoại đã được đăng ký",
                "passwordConfirm.required" => "Vui lòng nhập lại mật khẩu",
                "passwordConfirm.same" => "Mật khẩu không trùng khớp",
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 401,
                'error' => $validator->getMessageBag()
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
                'message' => "Bạn có thể đăng nhập ngay bây giờ"
            ]);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Đăng xuất thành công !!'
        ]);
    }
    public function check(){
        $user_role = auth('sanctum')->user()->role_as;
        $user = Users::where('role_as', $user_role)->first();
        return response()->json([
            'status'=>200,
            'users'=>$user
        ]);
    }
}
