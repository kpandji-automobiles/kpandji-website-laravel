<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
/** Connexion depuis l'icône utilisateur du header (fenêtre modale). Réutilise l'auth Laravel existante (guard web, table users). */
class AccountAuthController extends Controller
{
    private function t(string $k): string
    {
        return app()->getLocale() === 'en' ? (content("header.{$k}_en") ?: content("header.$k")) : content("header.$k");
    }

    private function fail(Request $r, string $msg, int $status)
    {
        return $r->expectsJson()
            ? response()->json(['error' => $msg], $status)
            : back()->withErrors(['login' => $msg], 'kpLogin')->withInput($r->only('email', 'remember'))->with('kp_login_open', true);
    }

    public function login(Request $r)
    {
        $email = Str::lower(trim((string) $r->input('email'))); $password = (string) $r->input('password');
        if ($email === '' || $password === '' || mb_strlen($email) > 190) return $this->fail($r, $this->t('login_e_required'), 422);
        $key = 'site-login:'.$email.'|'.$r->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) return $this->fail($r, str_replace(':s', (string) RateLimiter::availableIn($key), $this->t('login_e_throttle')), 429);
        if (! Auth::attempt(['email' => $email, 'password' => $password], $r->boolean('remember'))) {
            RateLimiter::hit($key, 60);
            return $this->fail($r, $this->t('login_e_invalid'), 422);
        }
        RateLimiter::clear($key);
        $r->session()->regenerate();
        $to = Auth::user()->workspaceUrl() ?? redirect()->intended(route('home'))->getTargetUrl();
        return $r->expectsJson() ? response()->json(['redirect' => $to]) : redirect()->to($to);
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect()->route('home');
    }
}
