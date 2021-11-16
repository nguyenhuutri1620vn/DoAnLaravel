<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            if ($request->user()->tokenCan('server:admin') || $request->user()->tokenCan('server:staff')) {
                return $next($request);
            } else {
                return response()->json([
                    'message' => 'Người dùng không có quyền truy cập.',
                ], 403);
            }
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Vui lòng đăng nhập để tiếp tục.'
            ]);
        }
    }
}
