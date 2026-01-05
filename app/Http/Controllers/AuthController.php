<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function sign_in(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if(Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            session(['groom_side' => 1]);
            return redirect()->route('admin.dashboard');
        }
        return back()->withErrors([
            'error' => 'Invalid email or password. Please try again.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
