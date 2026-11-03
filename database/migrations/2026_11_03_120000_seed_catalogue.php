<?php

use Database\Seeders\FirstDeploySeeder;
use Illuminate\Database\Migrations\Migration;

/**
 * Remplit le catalogue (voitures, modèles, showroom) au premier déploiement :
 * l'hébergement n'a pas de shell pour lancer db:seed. Une seule exécution, gardée par
 * la table migrations ; ignorée en tests, dont les bases doivent partir vides.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        (new FirstDeploySeeder)->setContainer(app())->__invoke();
    }

    public function down(): void
    {
        // Les données du catalogue restent : elles ont pu être modifiées depuis le backoffice.
    }
};
