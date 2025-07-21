<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Nếu không phải admin
        if (!$user || $user->is_admin != 1) {
            // Trả về lỗi 403 hoặc redirect
            abort(403, 'Bạn không có quyền truy cập.');
        }

        return $next($request);
    }
}
