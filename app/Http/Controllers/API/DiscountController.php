<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiscountController extends Controller
{
    public function index(){
        $discount = Discount::all();
        return response()->json([
            'status' => 200,
            'discount' => $discount,
        ]);
    }

    public function store(Request $request){
        $validator =  Validator::make($request->all(), [
            'name' => 'required|max:30|unique:discount,name',
            'percent' => 'required|numeric|max:100|min:0',
        ],
        [
            'name.required' => 'Vui lòng nhập tên mã giảm giá. ',
            'name.unique' => 'Tên mã giảm giá không được trùng với các mã trước đó',
            'name.max' => 'Tên mã giảm giá không được dài quá 30 ký tự',
            'percent.required' => 'Vui lòng nhập phần trăm giảm giá. ',
            'percent.numeric' => 'Phần trăm phải là ký tự số',
            'percent.max' => 'Phần trăm không được cao hơn 100%',
            'percent.min' => 'Phần trăm không được thấp hơn 0%',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->getMessageBag()
            ]);
        } else {
            $discount = new Discount();
            $discount->name = $request->input('name');
            $discount->percent = $request->input('percent');
            $discount->status = $request->input('status');

            $discount -> save();
            
            return response()->json([
                'status' => 200,
                'message' => 'Mã giảm giá đã được thêm'
            ]);
        }
    }

    public function destroy($id){
        $discount = Discount::find($id);
        if ($discount) {
            $discount->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Đã xóa mã giảm giá'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy mã giảm giá'
            ]);
        }
    }

    public function edit($id)
    {
        $discount = Discount::find($id);
        if ($discount) {
            return response()->json([
                'status' => 200,
                'discount' => $discount
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy mã giảm giá'
            ]);
        }
    }

    public function update(Request $request, $id){
        $validator =  Validator::make($request->all(), [
            'name' => 'required|max:30',
            'percent' => 'required|numeric|max:100|min:0',
        ],
        [
            'name.required' => 'Vui lòng nhập tên mã giảm giá. ',
            'name.max' => 'Tên mã giảm giá không được dài quá 30 ký tự',
            'percent.required' => 'Vui lòng nhập phần trăm giảm giá. ',
            'percent.numeric' => 'Phần trăm phải là ký tự số',
            'percent.max' => 'Phần trăm không được cao hơn 100%',
            'percent.min' => 'Phần trăm không được thấp hơn 0%',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->getMessageBag()
            ]);
        } else {

            $discount = Discount::find($id);

            if ($discount) {
                $discount->name = $request->input('name');
                $discount->percent = $request->input('percent');               
                $discount->status = $request->input('status');

                $discount->save();

                return response()->json([
                    'status' => 200,
                    'message' => 'Đã cập nhật mã giảm giá'
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy mã giảm giá'
                ]);
            }
        }
    }
}
