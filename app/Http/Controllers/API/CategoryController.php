<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $category = Category::all();
        return response()->json([
            'status' => 200,
            'category' => $category,
        ]);
    }


    public function store(Request $request)
    {
        $validator =  Validator::make($request->all(), [
            'meta_title' => 'required|max:191',
            'name' => 'required|max:30|unique:category,name',
            'slug' => 'required|max:191',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ],
        [
            'meta_title.required' => 'Vui lòng nhập meta title',
            'meta_title.max' => 'Meta title không dài quá 191 ký tự',
            'name.required' => 'Vui lòng nhập tên loại sản phẩm',
            'name.max' => 'Tên loại sản phẩm không được quá dài',
            'name.unique'=> "Tên loại sản phẩm đã được thêm",
            'slug.required' => "Vui lòng nhập slug loại sản phẩm",
            'slug.max' => "slug không được dài 191 ký tự",
            'image.required' => "Vui lòng thêm file ảnh",
            'image.image' => 'Vui lòng chọn file hình ảnh',
            'image.mimes' => 'Vui lòng chọn file có đuối: jpeg, png, jpg',
            'image.max' => 'File ảnh không quá 2MB'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->getMessageBag()
            ]);
        } else {

            $category = new Category;

            $category->meta_title = $request->input('meta_title');
            $category->meta_keyword = $request->input('meta_keyword');
            $category->meta_descrip = $request->input('meta_descrip');

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = $request->input('name') . '.' . $extension;
                $file->move('uploads/category/', $filename);
                $category->image = 'uploads/category/' . $filename;
            }

            $category->name = $request->input('name');
            $category->slug = $request->input('slug');
            $category->description = $request->input('description');
            $category->status = $request->input('status');

            $category->save();

            return response()->json([
                'status' => 200,
                'message' => 'Loại sản phẩm đã được thêm'
            ]);
        }
    }
    public function edit($id)
    {
        $category = Category::find($id);
        if ($category) {
            return response()->json([
                'status' => 200,
                'category' => $category
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy mã loại sản phẩm'
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator =  Validator::make($request->all(), [
            'meta_title' => 'required|max:191',
            'name' => 'required|max:191',
            'slug' => 'required|max:191',
        ],
        [
            'meta_title.required' => 'Vui lòng nhập meta title',
            'meta_title.max' => 'Meta title không dài quá 191 ký tự',
            'name.required' => 'Vui lòng nhập tên loại sản phẩm',
            'name.max' => 'Tên loại sản phẩm không được quá dài',
            'slug.required' => "Vui lòng nhập slug loại sản phẩm",
            'slug.max' => "Slug không được dài 191 ký tự",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->getMessageBag()
            ]);
        } else {

            $category = Category::find($id);

            if ($category) {
                $category->meta_title = $request->input('meta_title');
                $category->meta_keyword = $request->input('meta_keyword');
                $category->meta_descrip = $request->input('meta_descrip');
                $category->name = $request->input('name');

                if ($request->hasFile('image')) {
                    $path = $category->image;
                    if (File::exists($path)) {
                        File::delete($path);
                    }
                    $file = $request->file('image');
                    $extension = $file->getClientOriginalExtension();
                    $filename = $request->input('name') . '.' . $extension;
                    $file->move('uploads/category/', $filename);
                    $category->image = 'uploads/category/' . $filename;
                }

                $category->slug = $request->input('slug');
                $category->description = $request->input('description');
                $category->status = $request->input('status');

                $category->save();

                return response()->json([
                    'status' => 200,
                    'message' => 'Đã cập nhật loại sản phẩm'
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy mã loại sản phẩm'
                ]);
            }
        }
    }

    public function allcategory()
    {
        $category = Category::where('status', '1')->get();
        return response()->json([
            'status' => 200,
            'category' => $category,
        ]);
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if ($category) {
            $category->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Đã xóa loại sản phẩm'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy mã sản phẩm'
            ]);
        }
    }
}
