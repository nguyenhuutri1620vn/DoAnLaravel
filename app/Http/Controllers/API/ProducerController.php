<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Producer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProducerController extends Controller
{
    public function index()
    {
        $producer = Producer::all();
        return response()->json([
            'status' => 200,
            'producer' => $producer,
        ]);
    }

    public function store(Request $request)
    {
        $validator =  Validator::make($request->all(), [
            'meta_title' => 'required|max:191',
            'name' => 'required|max:191|unique:producer,name',
            'slug' => 'required|max:191',
        ],
        [
            'meta_title.required' => 'Vui lòng nhập meta title. ',
            'meta_title.max' => 'Meta title không dài quá 191 ký tự. ',
            'name.required' => 'Vui lòng nhập tên thương hiệu. ',
            'name.max' => 'Tên thương hiệu không được quá dài. ',
            'name.unique'=> "Tên thương hiệu đã được thêm. ",
            'slug.required' => "Vui lòng nhập slug thương hiệu. ",
            'slug.max' => "Slug không được dài 191 ký tự. ",
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->getMessageBag()
            ]);
        } else {

            $producer = new Producer;

            $producer->meta_title = $request->input('meta_title');
            $producer->meta_keyword = $request->input('meta_keyword');
            $producer->meta_descrip = $request->input('meta_descrip');
            $producer->name = $request->input('name');
            $producer->slug = $request->input('slug');
            $producer->description = $request->input('description');
            $producer->status = $request->input('status');

            $producer->save();

            return response()->json([
                'status' => 200,
                'message' => 'Đã thêm thương hiệu'
            ]);
        }
    }
    public function edit($id)
    {
        $producer = Producer::find($id);
        if ($producer) {
            return response()->json([
                'status' => 200,
                'producer' => $producer
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy mã thương hiệu'
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
            'meta_title.required' => 'Vui lòng nhập meta title. ',
            'meta_title.max' => 'Meta title không dài quá 191 ký tự. ',
            'name.required' => 'Vui lòng nhập tên thương hiệu. ',
            'name.max' => 'Tên thương hiệu không được quá dài. ',
            'slug.required' => "Vui lòng nhập slug thương hiệu. ",
            'slug.max' => "Slug không được dài 191 ký tự. ",
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->getMessageBag()
            ]);
        } else {

            $producer = Producer::find($id);

            if ($producer) {
                $producer->meta_title = $request->input('meta_title');
                $producer->meta_keyword = $request->input('meta_keyword');
                $producer->meta_descrip = $request->input('meta_descrip');
                $producer->name = $request->input('name');
                $producer->slug = $request->input('slug');
                $producer->description = $request->input('description');
                $producer->status = $request->input('status');

                $producer->save();

                return response()->json([
                    'status' => 200,
                    'message' => 'Đã thêm thương hiệu'
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy mã thương hiệu'
                ]);
            }
        }
    }

    public function allproducer()
    {
        $producer = Producer::where('status','1')->get();
        return response()->json([
            'status' => 200,
            'producer' => $producer,
        ]);
    }

    public function destroy($id)
    {
        $producer = Producer::find($id);
        if($producer){
            $producer->delete();
            return response()->json([
                'status'=>200,
                'message'=>'Đã xóa thương hiệu'
            ]);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'Không thấy mã thương hiệu'
            ]);
        }
    }
}
