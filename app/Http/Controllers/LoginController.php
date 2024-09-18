<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function LoginForm()
    {
        return view('pages.login.login');
    }

    public function login(Request $request)
    {
        // $credentials = $request->only('email', 'password');

        $user = User::where('username', $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);

            return redirect()->intended('/');
        }

        // if (Auth::attempt($credentials)) {
        //     return redirect()->intended('default_page');
        // }

        return back()->withErrors([
            'error_login' => 'The provided credentials do not match our records',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
