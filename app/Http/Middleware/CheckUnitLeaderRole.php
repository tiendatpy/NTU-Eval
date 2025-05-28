<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUnitLeaderRole
{
    /**
     * Kiểm tra người dùng có vai trò trưởng đơn vị hay không.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Kiểm tra người dùng đã đăng nhập chưa
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        // Kiểm tra người dùng có vai trò trưởng đơn vị không
        abort_if(!$user->role->isUnitLeader, 403, 'Bạn không có quyền truy cập.');


        return $next($request);
    }
}