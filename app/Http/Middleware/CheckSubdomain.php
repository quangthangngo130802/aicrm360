<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckSubdomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost(); // ví dụ: sgovn.crm360.dev hoặc crm360.dev
        $parts = explode('.', $host);

        // Nếu KHÔNG có subdomain (chỉ có crm360.dev)
        if (count($parts) < 3) {
            // Cho qua luôn → để vào trang đăng ký
            return $next($request);
        }

        // Có subdomain → kiểm tra trong bảng users
        $subdomain = implode('.', array_slice($parts, 0, -2)); // ví dụ: sgovn

        $exists = DB::table('users')->where('subdomain', $subdomain)->exists();

        if (!$exists) {
            // Nếu không có subdomain hợp lệ → trả về 404 hoặc trang lỗi
            return response()->view('errors.doamin', [], 404);
        }

        // Nếu có subdomain hợp lệ → Gán vào request và ép redirect về /login nếu đang ở /
        $request->attributes->set('subdomain', $subdomain);

        // Nếu đang truy cập "/" → chuyển hướng tới trang đăng nhập
        if ($request->path() == '/') {
            return redirect('/login');
        }

        return $next($request);
    }
}
