<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ContentController extends Controller
{
    public function index()
    {
        $news = Content::all();
        return response()->json([
            'status' => 200,
            'news' => $news,
        ]);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:content,name|max:191',
            'description' => 'required|max:3000',
            'meta_title' => 'required|unique:content,meta_title|max:191',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ],
        [
            'description.required' => "Vui lòng nhập mô tả tin tức. ",
            'description.max' => "Nội dung tin tức không được quá 3000 ký tự. ",
            'meta_title.required' => 'Vui lòng nhập meta title. ',
            'meta_title.max' => 'Meta title không dài quá 191 ký tự. ',
            'meta_title.unique' => "Meta title đã được thêm. ",
            'name.required' => 'Vui lòng nhập tên tin tức. ',
            'name.max' => 'Tên tin tức không được quá dài. ',
            'name.unique'=> "Tên tin tức đã được thêm. ",
            'slug.required' => "Vui lòng nhập slug tin tức. ",
            'slug.max' => "slug không được dài 191 ký tự. ",
            'image.required' => "Vui lòng thêm file ảnh. ",
            'image.image' => 'Vui lòng chọn file hình ảnh. ',
            'image.mimes' => 'Vui lòng chọn file có đuối: jpeg, png, jpg. ',
            'image.max' => 'File ảnh không quá 2MB. '
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->getMessageBag()
            ]);
        } else {
            $content = new Content;

            $content->name = $request->input('name');
            $content->description = $request->input('description');
            $content->status = $request->input('status');

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move('uploads/content/', $filename);
                $content->image = 'uploads/content/' . $filename;
            }

            $content->meta_title = $request->input('meta_title');
            $content->meta_keyword = $request->input('meta_keyword');
            $content->meta_descrip = $request->input('meta_descrip');

            $content->save();

            return response()->json([
                'status' => 200,
                'message' => 'Tin tức đã được thêm'
            ]);
        }
    }
    public function edit($id)
    {
        $content = Content::find($id);
        if ($content) {
            return response()->json([
                'status' => 200,
                'news' => $content
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy mã tin tức'
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'description' => 'required|max:3000',
            'meta_title' => 'required|max:191',
            'meta_keyword' => 'required|max:191',
        ],
        [
            'description.required' => "Vui lòng nhập mô tả tin tức. ",
            'description.max' => "Nội dung tin tức không được quá 3000 ký tự. ",
            'meta_title.required' => 'Vui lòng nhập meta title. ',
            'meta_title.max' => 'Meta title không dài quá 191 ký tự. ',
            'meta_title.unique' => "Meta title đã được thêm. ",
            'name.required' => 'Vui lòng nhập tên tin tức. ',
            'name.max' => 'Tên tin tức không được quá dài. ',
            'name.unique'=> "Tên tin tức đã được thêm. ",
            'slug.required' => "Vui lòng nhập slug tin tức. ",
            'slug.max' => "slug không được dài 191 ký tự. ",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->getMessageBag()
            ]);
        } else {
            $content = Content::find($id);
            if($content){
                $content->name = $request->input('name');
                $content->description = $request->input('description');
                $content->status = $request->input('status');
    
                if ($request->hasFile('image')) {
                    $path = $content->image;
                    if (File::exists($path)) {
                        File::delete($path);
                    }
                    $file = $request->file('image');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '.' . $extension;
                    $file->move('uploads/content/', $filename);
                    $content->image = 'uploads/content/' . $filename;
                }
    
                $content->meta_title = $request->input('meta_title');
                $content->meta_keyword = $request->input('meta_keyword');
                $content->meta_descrip = $request->input('meta_descrip');
    
                $content->save();
    
                return response()->json([
                    'status' => 200,
                    'message' => 'Đã cập nhật tin tức'
                ]);
            }else{
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy mã tin tức'
                ]);
            }
        }
    }

    public function destroy($id){
        $content = Content::find($id);
        if ($content) {
            $content->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Đã xóa tin tức'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy mã tin tức'
            ]);
        }
    }
}
