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
        $host = $request->getHost(); // ví dụ: thangngo13080211.crm360.dev
        $parts = explode('.', $host);

        // Nếu KHÔNG có subdomain (ví dụ: crm360.dev) → cho qua luôn
        if (count($parts) < 3) {
            return $next($request);
        }

        // Có subdomain → kiểm tra tồn tại
        $subdomain = implode('.', array_slice($parts, 0, -2)); // thangngo13080211

        $exists = DB::table('users')->where('subdomain', $subdomain)->exists();

        if (!$exists) {
            return response()->view('errors.doamin', [], 404);
        }

        // Gán subdomain vào request để controller có thể dùng
        $request->attributes->set('subdomain', $subdomain);

        // Nếu đang ở "/" thì redirect sang "/login"
        if ($request->path() === '/') {
            return redirect('/login');
        }

        return $next($request);
    }
}
