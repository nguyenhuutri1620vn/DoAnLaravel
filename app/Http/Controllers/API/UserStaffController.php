<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Users;

class UserStaffController extends Controller
{
    public function viewusers(){
        $users = Users::where('role_as','0')->get();
        return response()->json([
            'status' => 200,
            'users' => $users,
        ]);
    }
    public function viewstaff(){
        $staff = Users::where('role_as','1')->get();
        return response()->json([
            'status' => 200,
            'staff' => $staff,
        ]);
    }

    public function becomeAdmin($id){
        $user = Users::find($id);

        $user->role_as = 1 ;
        $user->save();

        return response()->json([
            'status'=>200,
            'message'=>'Cố gắng làm việc nhen'
        ]);
    }

    public function becomeUser($id){
        $staff = Users::find($id);

        $staff->role_as = 0 ;
        $staff->save();

        return response()->json([
            'status'=>200,
            'message'=>'Tạm biệt nhân viên !!'
        ]);
    }
}
