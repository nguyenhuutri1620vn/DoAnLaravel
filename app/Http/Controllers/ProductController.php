<?php

namespace App\Http\Controllers;


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
        $validator = Validator::make($request->all(), [
            'cateID' => 'required',
            'producerID' => 'required',
            'name' => 'required|max:191|unique:product,name',
            'meta_title' => 'required|max:191|unique:product,meta_title',
            'number' => 'required|numeric|max:200|min:1',
            'selling_price' => 'required|numeric|max:99999999|min:1000',
            'original_price' => 'numeric|max:99999999|min:1000',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ],
        [
            'cateID.required' => 'Vui lòng chọn loại sản phẩm',
            'producerID.required' => 'Vui lòng chọn thương hiệu',
            'name.required' => 'Vui lòng nhập tên sản phẩm',
            'name.max' => 'Tên sản phẩm không được quá dài',
            'name.unique'=> "Tên sản phẩm đã được thêm",
            'meta_title.required' => 'Vui lòng nhập meta title',
            'meta_title.max' => 'Meta title không dài quá 191 ký tự',
            'meta_title.unique' => 'Meta title đã tồn tại',
            'image.required' => "Vui lòng thêm file ảnh",
            'image.image' => 'Vui lòng chọn file hình ảnh',
            'image.mimes' => 'Vui lòng chọn file có đuối: jpeg, png, jpg',
            'image.max' => 'File ảnh không quá 2MB',
            'number.required'=>'Vui lòng nhập số lượng',
            'number.numberic'=>'Dữ liệu nhập vào phải là chữ số',
            'number.max'=>'Giá trị tiền tệ không hợp lệ',
            'number.min'=>'Giá trị tiền tệ không hợp lệ',
            'selling_price.required'=>"Vui lòng nhập giá bán",
            'selling_price.numberic'=>"Dữ liệu nhập vào phải là chữ số",
            'selling_price.max'=>"Giá trị tiền tệ không hợp lệ",
            'selling_price.min'=>"Giá trị tiền tệ không hợp lệ",
            'original_price.numberic'=>"Dữ liệu nhập vào phải là chữ số (không nhỏ hơn 1.000 VNĐ)",
            'original_price.max'=>"Giá trị tiền tệ không hợp lệ",
            'original_price.min'=>"Giá trị tiền tệ không hợp lệ (không nhỏ hơn 1.000 VNĐ)",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->getMessageBag(),
            ]);
        } else {
            $product = new Product;

            $product->cateID = $request->input('cateID');
            $product->producerID = $request->input('producerID');
            $product->name = $request->input('name');
            $product->description = $request->input('description');

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
            $product->selling_price = $request->input('selling_price');
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
        $validator = Validator::make($request->all(), [
            'cateID' => 'required|max:191',
            'producerID' => 'required|max:191',
            'name' => 'required|max:191',
            'meta_title' => 'required|max:191',
            'meta_keyword' => 'required|max:191',
            'number' => 'required|numeric|max:100|min:1',
            'selling_price' => 'required|numeric|max:99999999|min:1000',
            'original_price' => 'numeric|max:99999999|min:1000',
        ],
        [
            'cateID.required' => 'Vui lòng chọn loại sản phẩm',
            'producerID.required' => 'Vui lòng chọn thương hiệu',
            'name.required' => 'Vui lòng nhập tên sản phẩm',
            'name.max' => 'Tên sản phẩm không được quá dài',
            'meta_title.required' => 'Vui lòng nhập meta title',
            'meta_title.max' => 'Meta title không dài quá 191 ký tự',
            'number.required'=>'Vui lòng nhập số lượng',
            'number.numberic'=>'Dữ liệu nhập vào phải là chữ số',
            'number.max'=>'Giá trị tiền tệ không hợp lệ',
            'number.min'=>'Giá trị tiền tệ không hợp lệ ',
            'selling_price.required'=>"Vui lòng nhập giá bán",
            'selling_price.numberic'=>"Dữ liệu nhập vào phải là chữ số",
            'selling_price.max'=>"Giá trị tiền tệ không hợp lệ",
            'selling_price.min'=>"Giá trị tiền tệ không hợp lệ (không nhỏ hơn 1.000 VNĐ)",
            'original_price.numberic'=>"Dữ liệu nhập vào phải là chữ số",
            'original_price.max'=>"Giá trị tiền tệ không hợp lệ",
            'original_price.min'=>"Giá trị tiền tệ không hợp lệ (không nhỏ hơn 1.000 VNĐ)",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->getMessageBag()
            ]);
        } else {
            $product = Product::find($id);

            if ($product) {

                $product->cateID = $request->input('cateID');
                $product->producerID = $request->input('producerID');
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

                $product->number = $request->input('number');
                $product->original_price = $request->input('original_price');
                $product->selling_price = $request->input('selling_price');
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
