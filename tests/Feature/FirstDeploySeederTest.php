<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Modele;
use Database\Seeders\FirstDeploySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FirstDeploySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_database_gets_the_whole_catalogue(): void
    {
        $this->assertSame(0, Car::count());

        $this->seed(FirstDeploySeeder::class);

        $this->assertSame(8, Car::visible()->count());
        $this->assertEqualsCanonicalizing(['djetranplus', 'djetran', 'lathaye'], Modele::pluck('key')->all());
        $this->get('/modeles')->assertOk()->assertSee('LATHAYE');
        $this->get('/modeles/djetran-plus')->assertOk();
        $this->get('/showroom')->assertOk();
    }

    public function test_cars_edited_in_the_backoffice_are_kept_and_nothing_is_duplicated(): void
    {
        Car::create(['name' => 'LATHAYE', 'slug' => 'lathaye', 'category' => 'SUV', 'promo_price' => 21000000, 'is_active' => true, 'sort_order' => 1]);
        $modele = Modele::create(['key' => 'lathaye', 'name' => 'LATHAYE modifié', 'is_active' => true, 'sort' => 0]);

        $this->seed(FirstDeploySeeder::class);
        $this->seed(FirstDeploySeeder::class);

        $this->assertSame(1, Car::count());
        $this->assertSame(21000000, (int) Car::sole()->promo_price);
        $this->assertSame('LATHAYE modifié', $modele->fresh()->name);
        $this->assertSame(3, Modele::count());
    }

    public function test_the_migration_seeds_outside_tests_only(): void
    {
        $migration = require database_path('migrations/2026_11_03_120000_seed_catalogue.php');

        $migration->up();
        $this->assertSame(0, Car::count());

        $this->app['env'] = 'production';

        try {
            $migration->up();
        } finally {
            $this->app['env'] = 'testing';
        }

        $this->assertSame(8, Car::count());
        $this->assertSame(3, Modele::count());
    }
}
