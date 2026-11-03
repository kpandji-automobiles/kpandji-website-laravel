<?php

namespace Database\Seeders;

use App\Models\Car;
use Illuminate\Database\Seeder;

/**
 * Remplit le catalogue d'une base neuve (prod sans shell : lancé par la migration
 * 2026_11_03_120000_seed_catalogue au premier déploiement).
 *
 * CarSeeder réécrit les fiches existantes : il ne tourne que si la table cars est vide,
 * pour ne jamais écraser ce qui a été saisi dans le backoffice. ModeleSeeder ne touche
 * jamais une ligne existante et tourne toujours.
 */
class FirstDeploySeeder extends Seeder
{
    public function run(): void
    {
        if (Car::query()->doesntExist()) {
            $this->call(CarSeeder::class);
        }

        $this->call(ModeleSeeder::class);
    }
}
