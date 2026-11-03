<?php

namespace Tests\Feature\Backoffice;

use App\Filament\Auth\AdminLogin;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminLoginPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_footer_links_guests_to_the_backoffice_login(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('href="'.url('/backoffice/login').'"', false)
            ->assertSee('Espace admin');

        $this->actingAs(User::factory()->client()->create())->get('/')
            ->assertSee('Mon espace')
            ->assertDontSee('href="'.url('/backoffice/login').'"', false);
    }

    public function test_backoffice_login_is_branded_kpandji(): void
    {
        $this->get('/backoffice/login')
            ->assertOk()
            ->assertSee('Connexion — Espace administration')
            ->assertSee('Espace administration')
            ->assertSee('Accès réservé à l’équipe KPANDJI.')
            ->assertSee('prenom.nom@kpandji.com')
            ->assertSee(asset('css/kpandji/backoffice-login.css'), false)
            ->assertSee('kp-bo-login__brand', false)
            ->assertSee('Retour au site')
            ->assertSee(url('/manager/login'), false);
    }

    public function test_manager_login_keeps_its_own_look(): void
    {
        $this->get('/manager/login')
            ->assertOk()
            ->assertDontSee('backoffice-login.css', false)
            ->assertDontSee('kp-bo-login__brand', false);
    }

    public function test_admin_signs_in_through_the_branded_form(): void
    {
        $admin = User::factory()->admin()->create(['email' => 'findel@kpandji.test']);
        Filament::setCurrentPanel('admin');

        Livewire::test(AdminLogin::class)
            ->fillForm(['email' => 'findel@kpandji.test', 'password' => 'password'])
            ->call('authenticate')
            ->assertHasNoFormErrors()
            ->assertRedirect(url('/backoffice'));

        $this->assertAuthenticatedAs($admin);
    }
}
