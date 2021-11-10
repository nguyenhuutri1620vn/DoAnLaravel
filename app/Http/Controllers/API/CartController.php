<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function store(Request $request)
    {
        if (auth('sanctum')->check()) {
            $userID = auth('sanctum')->user()->id;
            $productID = $request->productID;
            $quantity = $request->quantity;

            $productCheck = Product::where('id', $productID)->first();
            if ($productCheck) {
                if (Cart::where('productID', $productID)->where('userID', $userID)->exists()) {
                    return response()->json([
                        'status' => 409,
                        'message' => "Mặt hàng ". $productCheck->name . " đã được thêm"
                    ]);
                } else {
                    $cartitem = new Cart;
                    $cartitem->userID = $userID;
                    $cartitem->productID = $productID;
                    $cartitem->quantity = $quantity;
                    $cartitem->save();

                    return response()->json([
                        'status' => 201,
                        'message' => "Thêm sản phẩm vào giỏ hàng thành công"
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 409,
                    'message' => "Không tìm thấy sản phẩm"
                ]);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => "Vui lòng đăng nhập để thêm giỏ hàng"
            ]);
        }
    }

    public function viewcart()
    {
        if (auth('sanctum')->check()) {
            $userID = auth('sanctum')->user()->id;
            $cartitem = Cart::where('userID', $userID)->get();
            return response()->json([
                'status' => 201,
                'cart' => $cartitem,
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'message' => "Vui lòng đăng nhập để xem giỏ hàng"
            ]);
        }
    }

    public function updatecart($cartID, $scope)
    {
        if (auth('sanctum')->check()) {
            $userID = auth('sanctum')->user()->id;
            $cartitem = Cart::where('id', $cartID)->where('userID', $userID)->first();
            if ($scope == 'inc') {
                $cartitem->quantity += 1;
            } else if ($scope == 'dec') {
                $cartitem->quantity -= 1;
            }
            $cartitem->update();
            return response()->json([
                'status' => 200,
                'message' => "Quantity updated"
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'message' => "Đăng nhập để tiếp tục"
            ]);
        }
    }

    public function deleteitemcart($cartID)
    {
        if (auth('sanctum')->check()) {
            $userID = auth('sanctum')->user()->id;
            $cartitem = Cart::where('id', $cartID)->where('userID', $userID)->first();
            if ($cartitem) {
                $cartitem->delete();
                return response()->json([
                    'status' => 200,
                    'message' => "Đã xóa sản phẩm khỏi giỏ hàng"
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => "Sản phẩm không tổn tại"
                ]);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => "Đăng nhập để tiếp tục"
            ]);
        }
    }
}
