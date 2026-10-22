<?php
namespace App\Policies;
use App\Models\Investment;
use App\Models\User;
/** Un client ne voit que ses propres investissements ; manager et admin voient et gèrent tout. */
class InvestmentPolicy
{
    public function view(User $u, Investment $i): bool { return $u->isManager() || $i->user_id === $u->id; }
    public function manage(User $u, ?Investment $i = null): bool { return $u->isManager(); }
}
