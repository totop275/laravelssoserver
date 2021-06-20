<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                if ($request->redirect_to) {
                    $user = Auth::user();
                    $allToken = is_array(session('all_token')) ? session('all_token') : [];
                    $token = auth('api')->login($user);
                    
                    $allToken[] = $token;
                    session(['all_token' => $allToken]);

                    $params = http_build_query(['token' => $token]);
                    return redirect($request->redirect_to . '?' . $params);
                }
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
