<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Content;
use App\Models\District;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
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
                    'provinceID.required' => "Vui lòng chọn tỉnh - thành phố. ",
                    'phone.required' => "Vui lòng nhập số điện thoại. ",
                    'phone.max' => "Số điện thoại không hợp lệ. ",
                    'phone.min' => 'Số điện thoại không hợp lệ. ',
                    'districtID.required' => 'Vui lòng chọn quận - huyện. ',
                    'address.required' => 'Vui lòng nhập nhập địa chỉ. ',
                    'address.max' => 'Địa chỉ không hợp lệ (quá dài). ',
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

                $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
                $order->tracking_no = substr(str_shuffle($permitted_chars), 0, 6);;

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
                    'provinceID.required' => "Vui lòng chọn tỉnh - thành phố. ",
                    'phone.required' => "Vui lòng nhập số điện thoại. ",
                    'phone.max' => "Số điện thoại không hợp lệ. ",
                    'phone.min' => 'Số điện thoại không hợp lệ. ',
                    'districtID.required' => 'Vui lòng chọn quận - huyện. ',
                    'address.required' => 'Vui lòng nhập nhập địa chỉ. ',
                    'address.max' => 'Địa chỉ không hợp lệ (quá dài). ',
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

    public function view()
    {
        $order = Order::all();
        return response()->json([
            'status' => 200,
            'order' => $order,
        ]);
    }
    public function updatewaitingorder($order_id)
    {
        // $orderwaiting = Order::find($order_id);
        $orderwaiting = Order::find($order_id);
        $orderwaiting->status = 1;
        $orderwaiting->save();
        return response()->json([
            'status' => 200,
            'message' => 'Đơn hàng đã được duyệt'
        ]);
    }
    public function updateshippingorder($order_id)
    {
        $orderwaiting = Order::find($order_id);
        $orderwaiting->status = 2;
        $orderwaiting->save();
        return response()->json([
            'status' => 200,
            'message' => 'Đơn hàng giao thành công'
        ]);
    }
    public function cancelorder($id)
    {
        $order = Order::find($id);
        $order->total_price = 0;
        $order->status = 3;
        $orderdetail = OrderDetail::where('orderID',  $id)->get();

        foreach ($orderdetail as $item) {
            $item->product->update([
                'number' => $item->product->number + $item->count
            ]);
        }
        if ($order) {
            OrderDetail::destroy($orderdetail);
            $order->save();

            return response()->json([
                'status' => 200,
                'message' => 'Đã xóa đơn hàng'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy mã đơn hàng'
            ]);
        }
    }
    public function getdashboard()
    {
        $order = Order::all();
        $content = Content::all();
        $product = Product::all();
        $user = Users::where('role_as', 1)->get();
        $order_month = [];
        for ($i = 1; $i <= 12; $i++) {
            $month = Order::whereMonth('created_at', $i)->whereYear('created_at', '2022')->count();
            array_push($order_month, $month);
        }
        $totalprice = [];
        for ($i = 1; $i <= 12; $i++) {
            $month = Order::whereMonth('created_at', $i)->whereYear('created_at', '2022')->sum('total_price');
            array_push($totalprice, $month);
        }
        $totalproductdashboard = [];
        $totalproductsold = OrderDetail::all()->sum('count');
        $totalproduct = Product::all()->sum('number');
        $totalhaventsold = $totalproduct - $totalproductsold;
        array_push($totalproductdashboard, $totalproductsold);
        array_push($totalproductdashboard, $totalhaventsold);

        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $orderday = Order::whereDate('created_at', date("Y-m-d"))->where('status', '<', '3')->count();
        $orderday_money = Order::whereDate('created_at', date("Y-m-d"))->where('status', '<', '3')->sum('total_price');
        $product_sold = OrderDetail::whereDate('created_at', date("Y-m-d"))->sum('count');
        $product_detai_sold = OrderDetail::whereDate('created_at', date("Y-m-d"))->get();
        return response()->json([
            'status' => 200,
            'order' => $order,
            'content' => $content,
            'product' => $product,
            'user' => $user,
            'month' => $order_month,
            'totalprice' => $totalprice,
            'orderday' => $orderday,
            'money_day' => $orderday_money,
            'productsold' => $product_sold,
            'productdetailsold' => $product_detai_sold,
            'totalproductdashboard' => $totalproductdashboard
        ]);
    }
    public function getday($from, $to)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $orderday = Order::whereBetween('created_at', [$from, "$to 23:59:59"])->where('status', '<', '3')->count();
        $orderday_money = Order::whereBetween('created_at', [$from, "$to 23:59:59"])->where('status', '<', '3')->sum('total_price');
        $product_sold = OrderDetail::whereBetween('created_at', [$from, "$to 23:59:59"])->sum('count');
        $product_detai_sold = OrderDetail::whereBetween('created_at', [$from, "$to 23:59:59"])->get();
        return response()->json([
            'status' => 200,
            'orderday' => $orderday,
            'money_day' => $orderday_money,
            'productsold' => $product_sold,
            'productdetailsold' => $product_detai_sold
        ]);
    }
    public function cancelordercus($id)
    {
        $order = Order::where('id', $id)->first();
        $orderdetail = OrderDetail::where('orderID',  $id)->get();

        foreach ($orderdetail as $item) {
            $item->product->update([
                'number' => $item->product->number + $item->count
            ]);
        }
        if ($order) {
            OrderDetail::destroy($orderdetail);
            $order->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Đã xóa đơn hàng'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy mã đơn hàng'
            ]);
        }
    }

    function getyear($year)
    {
        $order_month = [];
        for ($i = 1; $i <= 12; $i++) {
            $month = Order::whereMonth('created_at', $i)->whereYear('created_at', $year)->count();
            array_push($order_month, $month);
        }
        $totalprice = [];
        for ($i = 1; $i <= 12; $i++) {
            $month = Order::whereMonth('created_at', $i)->whereYear('created_at', $year)->sum('total_price');
            array_push($totalprice, $month);
        }
        return response()->json([
            'status' => 200,
            'month' => $order_month,
            'totalprice' => $totalprice,
        ]);
    }
    public function getdaystaff($day){
        $orderday = Order::whereDate('created_at', $day)->where('status', '<', '3')->count();
        $orderday_money = Order::whereDate('created_at', $day)->where('status', '<', '3')->sum('total_price');
        $product_sold = OrderDetail::whereDate('created_at', $day)->sum('count');
        $product_detai_sold = OrderDetail::whereDate('created_at', $day)->get();
        return response()->json([
            'status' => 200,
            'orderday' => $orderday,
            'money_day' => $orderday_money,
            'productsold' => $product_sold,
            'productdetailsold' => $product_detai_sold
        ]);
    }
}
