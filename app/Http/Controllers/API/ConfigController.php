<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ConfigController extends Controller
{
    public function edit($id)
    {
        $config = Config::find($id);
        if ($config) {
            return response()->json([
                'status' => 200,
                'config' => $config
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:30',
            'slogan' => 'required|max:150',
            'email' => 'required|email|max:191',
            'phone' => 'required|min:10',
            'address' => 'required|max:150',
            'price_ship' => 'required|max:191'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->getMessageBag()
            ]);
        } else {
            $config = Config::find($id);

            if ($config) {

                $config->name = $request->input('name');
                $config->slogan = $request->input('slogan');
                $config->email = $request->input('email');
                $config->address = $request->input('address');
                $config->phone = $request->input('phone');
                $config->price_ship = $request->input('price_ship');

                $config->save();

                return response()->json([
                    'status' => 200,
                    'message' => 'Updated config successfully'
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Can not updated config'
                ]);
            }
        }
    }
}
