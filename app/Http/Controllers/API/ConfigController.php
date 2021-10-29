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
                'message' => ''
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:30',
                'slogan' => 'required|max:150',
                'email' => 'required|email|max:30',
                'phone' => 'required|min:10',
                'address' => 'required|max:150',
            ],
            [
                'name.required' => 'Vui lòng nhập tên website',
                'name.max' => "Tên website không được hơn 30 ký tự",
                'slogan.required' => "Vui lòng nhập câu slogan cho website",
                'slogan.max' => '"Slogan không được dài hơn 150 ký tự',
                'email.required' => 'Vui lòng nhập email website',
                'email.email' => 'Email không đúng định dạng',
                'phone.required' => 'Vui lòng nhập số điện thoại',
                'email.max' => 'Email không được dài hơn 30 ký tự',
                'phone.min' => 'Số điện thoại không được nhỏ hơn 10 ký tự',
                'address.required' => "Vui lòng nhập địa chỉ website",
                'address.required' => 'Địa chỉ không được dài hơn 150 ký tự'
            ]
        );

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

                $config->save();

                return response()->json([
                    'status' => 200,
                    'message' => 'Đã cập nhật cấu hình website'
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Hiện tại không thể cập nhật cấu hình'
                ]);
            }
        }
    }
}
