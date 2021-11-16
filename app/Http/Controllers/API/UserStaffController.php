<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserStaffController extends Controller
{
    public function viewusers()
    {
        $users = Users::where('role_as', '0')->get();
        return response()->json([
            'status' => 200,
            'users' => $users,
        ]);
    }
    public function viewstaff()
    {
        $staff = Users::where('role_as', '1')->get();
        return response()->json([
            'status' => 200,
            'staff' => $staff,
        ]);
    }

    public function becomeAdmin($id)
    {
        $user = Users::find($id);

        $user->role_as = 1;
        $user->save();

        return response()->json([
            'status' => 200,
            'message' => 'Cố gắng làm việc nhen'
        ]);
    }

    public function becomeUser($id)
    {
        $staff = Users::find($id);

        $staff->role_as = 0;
        $staff->save();

        return response()->json([
            'status' => 200,
            'message' => 'Tạm biệt nhân viên !!'
        ]);
    }

    public function getStaff($id)
    {
        $staff = Users::find($id);
        if ($staff) {
            return response()->json([
                'status' => 200,
                'staff' => $staff
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy mã nhân viên'
            ]);
        }
    }
    public function updateStaff($id, Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'newpassword' => 'required | min:6 ',
                'confirmpassword' => 'required|required_with:newpassword|same:newpassword',
            ],
            [
                'newpassword.required' => 'Vui lòng nhập mật khẩu mới',
                'newpassword.min' => "Mật khẩu phải hơn 6 ký tự",
                'confirmpassword.required' => "Vui lòng nhập lại mật khẩu",
                'confirmpassword.same' => "Mật khẩu không trùng khớp",
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->getMessageBag()
            ]);
        } else {
            $staff = Users::find($id);
            $staff->password = Hash::make($request->newpassword);

            $staff->save();
            return response()->json([
                'status' => 200,
                'message' => "Đã cập nhật mật khẩu"
            ]);
        } 
    }
}
