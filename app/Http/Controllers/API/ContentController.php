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
            'meta_keyword' => 'required|unique:content,meta_keyword|max:191',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
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
                'message' => 'Created content successfully'
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
                'message' => 'Content ID not found'
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
                    'message' => 'Updated content successfully'
                ]);
            }else{
                return response()->json([
                    'status' => 404,
                    'message' => 'Content ID not found'
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
                'message' => 'Content deleted successfully'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Content ID not found'
            ]);
        }
    }
}
