<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\District;
use App\Models\Order;
use App\Models\Province;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function getUSer()
    {
        if (auth('sanctum')->check()) {
            $userID = auth('sanctum')->user()->id;
            $user = Users::where('id', $userID)->first();
            $province = Province::all();
            if ($user) {
                return response()->json([
                    'status' => 200,
                    'user' => $user,
                    'province' => $province
                ]);
            } else {
                return response()->json([
                    'status' => 401,
                    'message' => "Không tìm được mã khách hàng"
                ]);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => "Đăng nhập để tiếp tục"
            ]);
        }
    }
    public function selectdistrict($provinceid)
    {
        $district = District::where('provinceid', $provinceid)->get();
        return response()->json([
            'status' => 200,
            'district' => $district
        ]);
    }

    public function placeorder(Request $request)
    {
        if (auth('sanctum')->check()) {

            $validator = Validator::make(
                $request->all(),
                [
                    'provinceID' => 'required',
                    'phone' => 'required|max:10|min:10',
                    'districtID' => 'required',
                    'address' => 'required|max:255'
                ],
                [
                    'provinceID.required' => "Vui lòng chọn tỉnh - thành phố",
                    'phone.required' => "Vui lòng nhập số điện thoại",
                    'phone.max' => "Số điện thoại không hợp lệ",
                    'phone.min' => 'Số điện thoại không hợp lệ',
                    'districtID.required' => 'Vui lòng chọn quận - huyện',
                    'address.required' => 'Vui lòng nhập nhập địa chỉ',
                    'address.max' => 'Địa chỉ không hợp lệ (quá dài)',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'errors' => $validator->getMessageBag()
                ]);
            } else {
                $userid = auth('sanctum')->user()->id;
                $order = new Order;
                $order->userID =  $userid;
                $order->phone = $request->phone;
                $order->provinceID = $request->provinceID;
                $order->districtID = $request->districtID;
                $order->address = $request->address;
                $order->note = $request->note;
                $order->number = $request->number;
                $order->total_price = $request->total_price;
                $order->payment_mode = $request->payment_mode;
                $order->paymentID = $request->payment_id;


                $order->tracking_no = 'fundaecom' . rand(1111, 9999);
                $order->save();

                $cart = Cart::where('userID',  $userid)->get();
                $orderitem = [];
                foreach ($cart as $item) {
                    $orderitem[] = [
                        'productID' => $item->productID,
                        'count' => $item->quantity,
                        'price' => $item->product->selling_price,
                    ];

                    $item->product->update([
                        'number' => $item->product->number - $item->quantity
                    ]);
                }

                $order->orderitem()->createMany($orderitem);
                Cart::destroy($cart);

                return response()->json([
                    'status' => 200,
                    'message' => "Mua hàng thành công"
                ]);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => "Bạn cẩn đăng nhập để thanh toán"
            ]);
        }
    }

    public function validateorder(Request $request)
    {
        if (auth('sanctum')->check()) {
            $validator = Validator::make(
                $request->all(),
                [
                    'provinceID' => 'required',
                    'phone' => 'required|min:10|max:10',
                    'districtID' => 'required',
                    'address' => 'required|max:255'
                ],
                [
                    'provinceID.required' => "Vui lòng chọn tỉnh - thành phố",
                    'phone.required' => "Vui lòng nhập số điện thoại",
                    'phone.max' => "Số điện thoại không hợp lệ",
                    'phone.min' => 'Số điện thoại không hợp lệ',
                    'districtID.required' => 'Vui lòng chọn quận - huyện',
                    'address.required' => 'Vui lòng nhập nhập địa chỉ',
                    'address.max' => 'Địa chỉ không hợp lệ (quá dài)',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'errors' => $validator->getMessageBag()
                ]);
            } else {

                return response()->json([
                    'status' => 200,
                    'message' => "Xác nhận thông tin"
                ]);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => "Bạn cần đăng nhập để xác nhận thông tin"
            ]);
        }
    }
}
