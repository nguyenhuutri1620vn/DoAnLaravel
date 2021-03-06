<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return response()->json([
            'status' => 200,
            'products' => $products
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'cateID' => 'required',
                'producerID' => 'required',
                'discountID' => 'required',

                'name' => 'required|max:191|unique:product,name',
                'meta_title' => 'required|max:191|unique:product,meta_title',
                'number' => 'required|numeric|max:200|min:1',
                'original_price' => 'required|numeric|max:99999999999|min:1000',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'description' => 'required'
            ],
            [
                'cateID.required' => 'Vui lòng chọn loại sản phẩm. ',
                'producerID.required' => 'Vui lòng chọn thương hiệu. ',
                'discountID.required' => 'Vui lòng chọn mã giảm. ',
                'name.required' => 'Vui lòng nhập tên sản phẩm. ',
                'name.max' => 'Tên sản phẩm không được quá dài. ',
                'name.unique' => "Tên sản phẩm đã được thêm. ",
                'meta_title.required' => 'Vui lòng nhập meta title. ',
                'meta_title.max' => 'Meta title không dài quá 191 ký tự. ',
                'meta_title.unique' => 'Meta title đã tồn tại. ',
                'image.required' => "Vui lòng thêm file ảnh. ",
                'image.image' => 'Vui lòng chọn file hình ảnh. ',
                'image.mimes' => 'Vui lòng chọn file có đuối: jpeg, png, jpg. ',
                'image.max' => 'File ảnh không quá 2MB. ',
                'number.required' => 'Vui lòng nhập số lượng. ',
                'number.numeric' => 'Dữ liệu nhập vào phải là số. ',
                'number.max' => 'Giá trị tiền tệ không hợp lệ (<100). ',
                'number.min' => 'Giá trị tiền tệ không hợp lệ. ',
                'original_price.required' => "Vui lòng nhập giá bán. ",
                'original_price.numeric' => "Dữ liệu nhập vào phải là số. ",
                'original_price.max' => "Giá trị tiền tệ không hợp lệ. ",
                'original_price.min' => "Giá trị tiền tệ không hợp lệ. ",
                'description.required' => 'Vui lòng thêm mô tả'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->getMessageBag(),
            ]);
        } else {
            $product = new Product;
            $discount = Discount::where('id', $request->input('discountID'))->first();

            $product->cateID = $request->input('cateID');
            $product->producerID = $request->input('producerID');
            $product->discountID = $request->input('discountID');
            $product->name = $request->input('name');
            $product->description = $request->input('description');
            $product->video = $request->input('video');

            $product->meta_title = $request->input('meta_title');
            $product->meta_keyword = $request->input('meta_keyword');
            $product->meta_descrip = $request->input('meta_descrip');


            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move('uploads/product/', $filename);
                $product->image = 'uploads/product/' . $filename;
            }
            $product->number = $request->input('number');
            $product->original_price = $request->input('original_price');
            $product->selling_price = ($request->input('original_price') - ($request->input('original_price')*$discount->percent)/100);
            $product->featured = $request->input('featured');
            $product->popular = $request->input('popular');
            $product->status = $request->input('status');

            $product->save();

            return response()->json([
                'status' => 200,
                'message' => 'Đã thêm sản phẩm',
            ]);
        }
    }

    public function edit($id)
    {
        $product = Product::find($id);
        if ($product) {
            return response()->json([
                'status' => 200,
                'product' => $product,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy mã sản phẩm',
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'cateID' => 'required|max:191',
                'producerID' => 'required|max:191',
                'discountID' => 'required',

                'name' => 'required|max:191',
                'meta_title' => 'required|max:191',
                'meta_keyword' => 'required|max:191',
                'number' => 'required|numeric|max:100|min:1',
                'original_price' => 'required|numeric|max:99999999999|min:1000',
                'description' => 'required'

            ],
            [
                'cateID.required' => 'Vui lòng chọn loại sản phẩm. ',
                'producerID.required' => 'Vui lòng chọn thương hiệu. ',
                'discountID.required' => 'Vui lòng chọn mã giảm. ',

                'name.required' => 'Vui lòng nhập tên sản phẩm. ',
                'name.max' => 'Tên sản phẩm không được quá dài. ',
                'meta_title.required' => 'Vui lòng nhập meta title. ',
                'meta_title.max' => 'Meta title không dài quá 191 ký tự. ',
                'number.required' => 'Vui lòng nhập số lượng. ',
                'number.numeric' => 'Dữ liệu nhập vào phải là số. ',
                'number.max' => 'Giá trị tiền tệ không hợp lệ (<100). ',
                'number.min' => 'Giá trị tiền tệ không hợp lệ . ',
                'original_price.required' => "Vui lòng nhập giá bán. ",
                'original_price.numeric' => "Dữ liệu nhập vào phải là số. ",
                'original_price.max' => "Giá trị tiền tệ không hợp lệ. ",
                'original_price.min' => "Giá trị tiền tệ không hợp lệ (không nhỏ hơn 1.000 VNĐ). ",
                'description.required' => 'Vui lòng thêm mô tả'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->getMessageBag()
            ]);
        } else {
            $product = Product::find($id);
            $discount = Discount::where('id', $request->input('discountID'))->first();

            if ($product) {

                $product->cateID = $request->input('cateID');
                $product->producerID = $request->input('producerID');
                $product->discountID = $request->input('discountID');

                $product->name = $request->input('name');
                $product->description = $request->input('description');

                $product->meta_title = $request->input('meta_title');
                $product->meta_keyword = $request->input('meta_keyword');
                $product->meta_descrip = $request->input('meta_descrip');


                if ($request->hasFile('image')) {

                    $path = $product->image;
                    if (File::exists($path)) {
                        File::delete($path);
                    }
                    $file = $request->file('image');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '.' . $extension;
                    $file->move('uploads/product/', $filename);
                    $product->image = 'uploads/product/' . $filename;
                }
                $product->video = $request->input('video');
                $product->number = $request->input('number');
                $product->original_price = $request->input('original_price');
                $product->selling_price = ($request->input('original_price') - ($request->input('original_price')*$discount->percent)/100);
                $product->featured = $request->input('featured');
                $product->popular = $request->input('popular');
                $product->status = $request->input('status');

                $product->save();

                return response()->json([
                    'status' => 200,
                    'message' => 'Đã cập nhật sản phẩm',
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy mã sản phẩm',
                ]);
            }
        }
    }
    public function destroy($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Đã xóa sản phẩm'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy mã sản phẩm'
            ]);
        }
    }
}
