<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function index($id)
    {
        $comment = Comment::where('productID', $id)->get();
        $userid = auth('sanctum')->user()->id;
        $order = Order::where('userID', $userid)->get('id');
        $getorder = [];
        foreach ($order as $value) {
            array_push($getorder, $value->id);
        }

        $getorderdetail = [];
        for($i = 0; $i < count($getorder); $i++){
            $orderdetail = OrderDetail::where('orderID', $getorder[$i])->get('productID');
            foreach ($orderdetail as $value) {
                array_push($getorderdetail, $value->productID);
            }
        }

        return response()->json([
            'status' => 200,
            'listcomment' => $comment,
            'getorderdetail' => $getorderdetail
        ]);
    }

    public function store(Request $request, $id)
    {

        $userid = auth('sanctum')->user()->id;

        $hadcomment = Comment::where('userID', $userid)->where('productID', $id)->get();

        $validator = Validator::make($request->all(), [
            'content' => 'required|max:191',
            'detail' => 'required|min:10|max:256',
            'rate' => 'required'
        ], [
            'content.required' => 'Vui lòng nhập tiêu đề bình luận',
            'content.max' => 'Tiêu đề bình luân không được quá 191 ký tự',
            'detail.required' => 'Vui lòng nhập chi tiết bình luận',
            'detail.min' => 'Chi tiết bình luận không được ít hơn 10 ký tự',
            'detail.max' => 'Chi tiết bình luận không được nhiều hơn 256 ký tự',
            'rate' => 'Vui lòng đánh giá sản phẩm'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->getMessageBag()
            ]);
        } else if (count($hadcomment) >= 1) {
            return response()->json([
                'status' => 201,
                'message' => 'Chỉ được bình luận sản phẩm một lần'
            ]);
        } else {
            $comment = new Comment;
            $comment->content = $request->input('content');
            $comment->detail = $request->input('detail');
            $comment->rate = $request->input('rate');
            $comment->userID = $userid;
            $comment->productID = $id;

            $comment->save();

            return response()->json([
                'status' => 200,
                'message' => 'Cảm ơn quý khách đã đánh giá sản phẩm',
            ]);
        }
    }
}
