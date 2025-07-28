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

        if (count($parts) < 3) {
            if ($request->is('login')) {
                return redirect('/');
            }

            return $next($request);
        }

        $subdomain = implode('.', array_slice($parts, 0, -2));

        $exists = DB::table('users')->where('subdomain', $subdomain)->exists();

        if (!$exists) {
            return response()->view('errors.doamin', [], 404);
        }


        $request->attributes->set('subdomain', $subdomain);

        if ($request->path() === '' || $request->path() === '/') {
            return redirect('/login');
        }

        return $next($request);
    }
}
