<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $host = $request->getHost();
        $parts = explode('.', $host);

        // Nếu KHÔNG có subdomain
        if (count($parts) < 3) {
            // Nếu người dùng cố truy cập /login trên domain gốc → redirect về trang đăng ký
            if ($request->is('login')) {
                return redirect('/');
            }

            // Các route khác như /, /dang-ky, ... vẫn cho qua
            return $next($request);
        }

        // Có subdomain → kiểm tra tồn tại
        $subdomain = implode('.', array_slice($parts, 0, -2));

        $exists = DB::table('users')->where('subdomain', $subdomain)->exists();

        if (!$exists) {
            return response()->view('errors.doamin', [], 404);
        }

        // Gán subdomain vào request
        $request->attributes->set('subdomain', $subdomain);

        // Nếu đang ở / thì redirect sang /login
        if ($request->path() === '' || $request->path() === '/') {
            return redirect('/login');
        }

        return $next($request);
    }
}
