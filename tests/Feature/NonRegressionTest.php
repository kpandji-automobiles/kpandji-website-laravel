<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Non-régression de la refonte : chaque page sans paramètre s'ouvre pour le rôle prévu.
 * Les comportements de chaque page sont testés dans leurs propres fichiers.
 */
class NonRegressionTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{?string, string}> */
    public static function pages(): array
    {
        $public = ['/', '/modeles', '/showroom', '/opportunities', '/kpandji-automobiles', '/ecologie', '/essai', '/demande-essai', '/contact', '/sav', '/sira', '/privilege', '/emplois', '/connexion'];
        $admin = ['/backoffice', '/backoffice/cars', '/backoffice/cars/create', '/backoffice/contenus', '/backoffice/job-applications', '/backoffice/modeles', '/backoffice/modeles/create', '/backoffice/pages', '/backoffice/pages/create', '/backoffice/privilege-requests', '/backoffice/promotion', '/backoffice/sav-requests', '/backoffice/sira-requests', '/backoffice/test-drive-requests'];
        $manager = ['/manager', '/manager/boite/autres', '/manager/boite/contacts', '/manager/boite/reservations', '/manager/boite/sav', '/manager/clients', '/manager/comptes', '/manager/conversations', '/manager/investissements', '/manager/investissements/create', '/manager/invitations'];
        $client = ['/mon-espace', '/mon-espace/gains', '/mon-espace/gains.csv', '/mon-espace/messages', '/mon-espace/opportunites', '/mon-compte/mot-de-passe'];

        $cases = [];
        foreach (['visiteur' => [null, $public], 'admin' => ['admin', $admin], 'manager' => ['manager', $manager], 'client' => ['client', $client]] as $label => [$role, $urls]) {
            foreach ($urls as $url) {
                $cases[$label.' '.$url] = [$role, $url];
            }
        }

        return $cases;
    }

    #[DataProvider('pages')]
    public function test_page_opens_for_its_role(?string $role, string $url): void
    {
        Car::create(['name' => 'LATHAYE', 'slug' => 'lathaye', 'category' => 'SUV', 'promo_price' => 22600000, 'is_active' => true, 'sort_order' => 1]);

        if ($role !== null) {
            $this->actingAs(User::factory()->{$role}()->create());
        }

        $this->get($url)->assertOk();
    }

    public function test_every_visible_model_page_opens(): void
    {
        foreach (['djetran', 'lathaye', 'souralai'] as $order => $slug) {
            Car::create(['name' => strtoupper($slug), 'slug' => $slug, 'category' => 'SUV', 'is_active' => true, 'sort_order' => $order]);
        }

        Car::visible()->get()->each(fn (Car $car) => $this->get(route('cars.show', $car))->assertOk());
    }
}
