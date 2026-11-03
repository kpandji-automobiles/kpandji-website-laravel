<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        putenv('SEED_ADMIN_PASSWORD');
        putenv('SEED_ADMIN_EMAIL');

        parent::tearDown();
    }

    public function test_seeder_creates_the_findel_admin_who_reaches_both_panels(): void
    {
        $this->artisan('db:seed', ['--class' => AdminSeeder::class])
            ->expectsOutputToContain('Compte admin créé : findel@kpandji.test')
            ->expectsOutputToContain('Mot de passe généré')
            ->assertSuccessful();

        $admin = User::where('email', 'findel@kpandji.test')->sole();
        $this->assertSame('Findel', $admin->name);
        $this->assertTrue($admin->isAdmin());
        $this->assertSame('active', $admin->account_status);
        $this->assertStringStartsWith('$2y$', $admin->password);

        $this->actingAs($admin)->get('/backoffice')->assertOk();
        $this->get('/manager')->assertOk();
    }

    public function test_admin_signs_in_with_the_password_and_email_given_in_the_environment(): void
    {
        putenv('SEED_ADMIN_EMAIL=Findel.Admin@Example.test');
        putenv('SEED_ADMIN_PASSWORD=mot-de-passe-fourni-2026');

        $this->artisan('db:seed', ['--class' => AdminSeeder::class])->doesntExpectOutputToContain('Mot de passe généré')->assertSuccessful();

        $this->post(route('account.login'), ['email' => 'findel.admin@example.test', 'password' => 'mot-de-passe-fourni-2026'])
            ->assertRedirect(url('/backoffice'));
        $this->assertAuthenticated();
    }

    public function test_second_run_keeps_the_existing_password_and_restores_the_admin_role(): void
    {
        $existing = User::factory()->client()->create(['email' => 'findel@kpandji.test', 'name' => 'Ancien nom']);
        $hash = $existing->password;

        $this->artisan('db:seed', ['--class' => AdminSeeder::class])->expectsOutputToContain('Compte admin mis à jour')->doesntExpectOutputToContain('Mot de passe généré')->assertSuccessful();
        $this->artisan('db:seed', ['--class' => AdminSeeder::class])->assertSuccessful();

        $admin = User::where('email', 'findel@kpandji.test')->sole();
        $this->assertSame($hash, $admin->password);
        $this->assertSame('Findel', $admin->name);
        $this->assertTrue($admin->isAdmin());
        $this->assertSame(1, User::count());
    }
}
