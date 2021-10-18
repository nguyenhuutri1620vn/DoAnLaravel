<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Province;
use App\Models\Users;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function getUSer()
    {
        if (auth('sanctum')->check()) {
            $userID = auth('sanctum')->user()->id;
            $province = Province::all();
            $user = Users::where('id', $userID)->first();
            if ($user) {
                return response()->json([
                    'status' => 200,
                    'user' => $user,
                    'province' => $province,
                ]);
            } else {
                return response()->json([
                    'status' => 401,
                    'message' => "Can not found User ID"
                ]);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => "Please login to add item to cart"
            ]);
        }
    }
    public function selectdistrict($provinceid)
    {
        $district = District::where('provinceid', $provinceid)->get();
        return response()->json([
            'status' => 200,
            'district' => $district
        ]);
    }
}
