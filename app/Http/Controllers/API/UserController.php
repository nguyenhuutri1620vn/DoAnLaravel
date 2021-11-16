<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Province;
use App\Models\Users;
use App\Rules\MatchOldPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function allprovince()
    {
        $province = Province::all();
        return response()->json([
            'status' => 200,
            'province' => $province,
        ]);
    }

    public function alldistict($province_id)
    {
        $district = District::where('provinceid', $province_id)->get();
        if ($district) {
            return response()->json([
                'status' => 200,
                'district' => $district,
            ]);
        }
    }

    public function updateprofile(Request $request)
    {
        if (auth('sanctum')->check()) {
            $validator = Validator::make($request->all(), [
                'fullname' => 'required | max: 255',
                'email' => 'required|email|max:191',
                'phone' => 'required|max:10|min:10',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'errors' => $validator->getMessageBag()
                ]);
            } else {
                $userid = auth('sanctum')->user()->id;

                $user = Users::find($userid);
                $user->fullname = $request->fullname;
                $user->phone = $request->phone;
                $user->email = $request->email;

                $user->save();

                return response()->json([
                    'status' => 200,
                    'message' => "Đã cập nhật thông tin cá nhân"
                ]);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => "Khách hàng phải đăng nhập hệ thống"
            ]);
        }
    }

    public function changepassword(Request $request)
    {
        if (auth('sanctum')->check()) {
            $validator = Validator::make($request->all(), [
                'currentpassword' => ['required',new MatchOldPassword],
                'newpassword' => 'required | min:6 ',
                'confirmpassword' => 'required|required_with:newpassword|same:newpassword',
            ],
            [
                'currentpasword.required' => "Vui lòng nhập mật khẩu hiện tại",
                'newpassword.required' => 'Vui lòng nhập mật khẩu mới',
                'newpassword.min' => "Mật khẩu phải hơn 6 ký tự",
                'confirmpassword.required' => "Vui lòng nhập lại mật khẩu",
                'confirmpassword.same' => "Mật khẩu không trùng khớp",
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'error' => $validator->getMessageBag()
                ]);
            } else {
                // $userID = auth('sanctum')->user()->id;

                // $user = Users::where('id', $userID)->first();
                $userid = auth('sanctum')->user()->id;

                $user = Users::find($userid);
                $user->password = Hash::make($request->newpassword);

                $user->save();
                return response()->json([
                    'status' => 200,
                    'message' => "Đã cập nhật mật khẩu"
                ]);
            }
            // }
        } else {
            return response()->json([
                'status' => 419,
                'message' => ""
            ]);
        }
    }
    public function orderhistory()
    {
        if (auth('sanctum')->check()) {
            $userid = auth('sanctum')->user()->id;
            $order = Order::where('userID', $userid)->get();
            return response()->json([
                'status' => 200,
                'order' => $order
            ]);
        } else {
            return response()->json([
                'status' => 419,
                'message' => "Khách hàng phải đăng nhập hệ thống"
            ]);
        }
    }
    public function orderitem($id)
    {
        if (auth('sanctum')->check()) {
            $order = Order::where('id', $id)->first();
            $orderitem = OrderDetail::where('orderID', $id)->get();
            return response()->json([
                'status' => 200,
                'orderitem' => $orderitem,
                'order'=>$order
            ]);
        } else {
            return response()->json([
                'status' => 419,
                'message' => "Khách hàng phải đăng nhập hệ thống"
            ]);
        }
    }
}
