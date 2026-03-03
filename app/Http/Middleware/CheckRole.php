<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Kiểm tra quyền truy cập dựa trên role của User.
     * Sử dụng: middleware('role:admin') hoặc middleware('role:admin,editor')
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Danh sách role được phép truy cập
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Chưa đăng nhập thì chuyển về trang login
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Kiểm tra role của user có nằm trong danh sách được phép không
        if (!in_array($request->user()->role, $roles)) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        return $next($request);
    }
}
