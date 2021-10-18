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
            'name' => 'required|max:191',
            'slug' => 'required|max:191',
            'description' => 'required'
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
                'message' => 'Created producer successfully'
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
                'message' => 'Producer ID not found'
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
                    'message' => 'Created producer successfully'
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'producer ID not found'
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
                'message'=>'Producer deleted successfully'
            ]);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'Producer ID not found'
            ]);
        }
    }
}
