<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) return redirect()->route('admin.dashboard');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'   => 'required|email',
            'password'=> 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            $user->update(['last_login_at' => now()]);
            LoginLog::create([
                'user_id'     => $user->id,
                'email'       => $user->email,
                'status'      => 'success',
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
                'logged_in_at'=> now(),
            ]);
            return redirect()->intended(route('admin.dashboard'));
        }

        LoginLog::create([
            'email'      => $request->email,
            'status'     => 'failed',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'logged_in_at'=> now(),
        ]);

        return back()->withErrors(['email' => 'بيانات الاعتماد غير صحيحة.'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
