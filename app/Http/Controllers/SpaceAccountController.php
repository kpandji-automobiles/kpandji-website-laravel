<?php
namespace App\Http\Controllers;
use App\Models\ClientInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
/** Changement de mot de passe (manager + client) et acceptation des invitations client. */
class SpaceAccountController extends Controller
{
    /** Page d'attente : compte invité pas encore validé (ou refusé) par un manager. */
    public function waiting(Request $r)
    {
        $u = $r->user();
        if ($u->isActive()) return redirect($u->workspaceUrl() ?? route('home'));
        return view('spaces.waiting', ['u' => $u]);
    }

    public function password() { return view('spaces.password'); }

    public function updatePassword(Request $r)
    {
        $r->validate(['current' => 'required|string', 'password' => 'required|string|min:8|confirmed']);
        if (! Hash::check($r->current, $r->user()->password)) return back()->withErrors(['current' => sp_t('pw_wrong')]);
        $r->user()->forceFill(['password' => $r->password])->save();
        return back()->with('ok', sp_t('pw_done'));
    }

    private function valid(string $token): ?ClientInvitation
    {
        $inv = ClientInvitation::findByToken($token);
        return $inv && $inv->state() === 'pending' && ! User::where('email', $inv->email)->exists() ? $inv : null;
    }

    public function show(Request $r, string $token)
    {
        $inv = $this->valid($token);
        return $inv ? view('spaces.invitation', ['inv' => $inv]) : response()->view('spaces.invitation', ['inv' => null], 410);
    }

    public function accept(Request $r, string $token)
    {
        $inv = $this->valid($token) ?? abort(410);
        $r->validate(['password' => 'required|string|min:8|confirmed']);
        $user = DB::transaction(function () use ($inv, $r) {
            $u = User::create(['name' => $inv->name, 'email' => $inv->email, 'phone' => $inv->phone, 'password' => $r->password, 'role' => in_array($inv->role, ['client', 'manager'], true) ? $inv->role : 'client', 'account_status' => 'pending']);
            $inv->forceFill(['accepted_at' => now(), 'user_id' => $u->id])->save();
            return $u;
        });
        Auth::login($user); $r->session()->regenerate();
        return redirect()->route('space.waiting');
    }
}
