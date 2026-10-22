<?php
namespace App\Console\Commands;
use App\Models\User;
use Illuminate\Console\Command;
class CreateAdmin extends Command
{
    protected $signature = 'kpandji:admin';
    protected $description = "Crée / met à jour l'administrateur à partir de ADMIN_EMAIL et ADMIN_PASSWORD (.env)";
    public function handle(): int
    {
        $email = env('ADMIN_EMAIL'); $pass = env('ADMIN_PASSWORD');
        if (!$email || !$pass) { $this->error('ADMIN_EMAIL et ADMIN_PASSWORD doivent être définis dans .env'); return 1; }
        $u = User::firstOrNew(['email' => $email]);
        $u->forceFill(['name' => $u->name ?: 'Administrateur KPANDJI', 'password' => $pass, 'is_admin' => true])->save();
        $this->info("Administrateur prêt : $email");
        return 0;
    }
}
