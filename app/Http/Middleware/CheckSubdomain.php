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
        $host = $request->getHost(); // ví dụ: demo.crm360.dev

        $parts = explode('.', $host);

        // Nếu domain không đủ 3 phần thì không phải subdomain
        if (count($parts) < 3) {
            abort(404, 'Tên miền không hợp lệ');
        }

        // Tách main domain và subdomain
        $mainDomain = implode('.', array_slice($parts, -2));     // crm360.dev
        $subdomain  = implode('.', array_slice($parts, 0, -2));  // demo (hoặc nhiều cấp: demo.abc)

        // Kiểm tra subdomain có tồn tại trong bảng users
        $exists = DB::table('users')->where('subdomain', $subdomain)->exists();

        if (!$exists) {
            abort(404, 'Subdomain không tồn tại');
        }

        // Gán subdomain vào request
        $request->attributes->set('subdomain', $subdomain);

        return $next($request);
    }
}
