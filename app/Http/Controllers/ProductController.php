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
            'cateID' => 'required|max:191',
            'producerID' => 'required|max:191',
            'name' => 'required|max:191',
            'meta_title' => 'required|max:191',
            'number' => 'required|numeric|max:200|min:1',
            'selling_price' => 'required|numeric|max:1000000|min:1',
            'original_price' => 'required|numeric|max:99999999|min:1',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
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
                'message' => 'Created product successfully',
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
                'message' => 'Product do not found',
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
            'selling_price' => 'required|numeric|max:1000000|min:1',
            'original_price' => 'required|numeric|max:99999999|min:1',
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
                    'message' => 'Updated product successfully',
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Product do not found',
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
                'message' => 'Product deleted successfully'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Product ID not found'
            ]);
        }
    }
}
