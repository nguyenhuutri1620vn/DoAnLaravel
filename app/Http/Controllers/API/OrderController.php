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
            $province = Province::all();
            $user = Users::where('id', $userID)->first();
            if ($user) {
                return response()->json([
                    'status' => 200,
                    'user' => $user,
                    'province' => $province,
                ]);
            } else {
                return response()->json([
                    'status' => 401,
                    'message' => "Can not found User ID"
                ]);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => "Please login to add item to cart"
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
            $validator = Validator::make($request->all(), [
                'provinceID' => 'required',
                'phone' => 'required|min:10|max:10',
                'districtID' => 'required',
                'address' => 'required|max:255'
            ]);

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
                    'message' => "Order Placed Successfully"
                ]);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => "You must login to confirm checkout"
            ]);
        }
    }

    public function validateorder(Request $request)
    {
        if (auth('sanctum')->check()) {
            $validator = Validator::make($request->all(), [
                'provinceID' => 'required',
                'phone' => 'required|min:10|max:10',
                'districtID' => 'required',
                'address' => 'required|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'errors' => $validator->getMessageBag()
                ]);
            } else {

                return response()->json([
                    'status' => 200,
                    'message' => "Form validate Successfully"
                ]);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => "You must login to confirm checkout"
            ]);
        }
    }
}
