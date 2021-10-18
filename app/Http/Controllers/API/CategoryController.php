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
            'name' => 'required|max:191',
            'slug' => 'required|max:191',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
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
                'message' => 'Created category successfully'
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
                'message' => 'Category ID not found'
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator =  Validator::make($request->all(), [
            'meta_title' => 'required|max:191',
            'name' => 'required|max:191',
            'slug' => 'required|max:191',

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
                    'message' => 'Created category successfully'
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Category ID not found'
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
                'message' => 'Category deleted successfully'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Category ID not found'
            ]);
        }
    }
}
