<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers {
        logout as traitlogout;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function authenticated(Request $request, $user)
    {
        $redirectTo = request()->redirect_to;
        if ($redirectTo) {
            $allToken = is_array(session('all_token')) ? session('all_token') : [];
            $token = auth('api')->login($user);
            
            $allToken[] = $token;
            session(['all_token' => $allToken]);

            $params = http_build_query(['token' => $token]);
            $redirectTo = $request->redirect_to . '?' . $params;
        }
        return redirect($redirectTo ?: route('home'));
    }

    public function logout(Request $request)
    {
        $tokens = is_array(session('all_token')) ? session('all_token') : [];
        foreach ($tokens as $token) {
            auth('api')->setToken($token)->invalidate();
        }
        return $this->traitlogout($request);
    }
}
