<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RestrictAdminWriteActions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Nếu là admin và đang cố gắng thực hiện POST, PUT, PATCH, DELETE => chặn lại
        if ($user && $user->is_admin == 1) {
            abort(403, 'Bạn không có quyền truy cập.');
        }

        return $next($request);
    }
}
