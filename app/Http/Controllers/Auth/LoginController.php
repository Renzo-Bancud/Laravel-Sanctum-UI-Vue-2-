<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Activitylog;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        // Create activity log entry for login
        Activitylog::create([
            'user_id' => $user->id,
            'activity_type' => 'Login',
            'activity_description' => 'User logged in',
            'user_agent' => $request->userAgent(),
            'ip_address' => $request->ip(),
        ]);

        Auth::user()->update(['isOnline' => true]);

        // Continue with the default authenticated method
        return redirect()->intended($this->redirectPath());
    }


    public function logout(Request $request)
    {
        // Before logout, update the isOnline column to 0
        if (Auth::check()) {
            Auth::user()->update(['isOnline' => false]);
        }

        // Perform logout action
        Auth::logout();

        // Redirect the user after logout
        return redirect()->route('login');
    }

}