<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Crée (ou remet en admin) le compte « findel » : accès à /backoffice et à /manager.
 *
 * Lancement : php artisan db:seed --class=AdminSeeder
 * Variables d'environnement facultatives : SEED_ADMIN_EMAIL (identifiant de connexion),
 * SEED_ADMIN_PASSWORD. Sans mot de passe fourni, un mot de passe aléatoire est généré à la
 * création et affiché une seule fois dans le terminal ; un compte existant garde le sien.
 * Aucun mot de passe n'est écrit dans le dépôt. Non appelé par DatabaseSeeder.
 */
class AdminSeeder extends Seeder
{
    /** Attributs du compte admin, aussi utilisés par la finalisation du déploiement (DeployController). */
    public const ATTRIBUTES = ['name' => 'Findel', 'role' => 'admin', 'is_admin' => true, 'account_status' => 'active'];

    public function run(): void
    {
        $email = Str::lower(trim((string) env('SEED_ADMIN_EMAIL', 'findel@kpandji.test')));
        $provided = (string) env('SEED_ADMIN_PASSWORD', '');

        $user = User::firstOrNew(['email' => $email]);
        $created = ! $user->exists;
        $password = $provided !== '' ? $provided : ($created ? Str::password(16, symbols: false) : null);

        $user->forceFill(self::ATTRIBUTES);

        if ($password !== null) {
            $user->password = $password;
        }

        $user->save();

        $this->command?->info(($created ? 'Compte admin créé' : 'Compte admin mis à jour').' : '.$email);

        if ($password !== null && $provided === '') {
            $this->command?->warn('Mot de passe généré (affiché une seule fois) : '.$password);
        }
    }
}
