<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
class AuthController extends Controller
{
    public function showLogin() { return Auth::check() && Auth::user()->is_admin ? redirect()->route('admin.dashboard') : view('admin.login'); }
    public function login(Request $r)
    {
        $cred = $r->validate(['email' => 'required|email', 'password' => 'required|string']);
        $key = 'admin-login:' . Str::lower($cred['email']) . '|' . $r->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors(['email' => 'Trop de tentatives. Réessayez dans ' . RateLimiter::availableIn($key) . ' secondes.'])->onlyInput('email');
        }
        if (Auth::attempt($cred + ['is_admin' => true], $r->boolean('remember'))) {
            RateLimiter::clear($key);
            $r->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }
        RateLimiter::hit($key, 60);
        return back()->withErrors(['email' => 'Identifiants incorrects.'])->onlyInput('email');
    }
    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect()->route('login')->with('status', 'Vous êtes déconnecté.');
    }
}
