<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class DeployFinalizeTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = 'test-deploy-token-0123456789abcdef';

    private const RELEASE = '20261103120000-abc1234';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.deploy.token' => self::TOKEN]);
        file_put_contents(base_path('RELEASE'), self::RELEASE."\n");
    }

    protected function tearDown(): void
    {
        @unlink(base_path('RELEASE'));

        parent::tearDown();
    }

    public function test_it_is_hidden_when_no_token_is_configured(): void
    {
        config(['services.deploy.token' => '']);

        $this->withToken('')->postJson(route('deploy.finalize'), ['release' => self::RELEASE])
            ->assertNotFound();
    }

    public function test_it_rejects_a_wrong_token(): void
    {
        Artisan::shouldReceive('call')->never();

        $this->withToken('wrong-token')->postJson(route('deploy.finalize'), ['release' => self::RELEASE])
            ->assertNotFound();
    }

    public function test_it_asks_to_retry_while_the_previous_release_is_still_served(): void
    {
        Artisan::shouldReceive('call')->never();

        $this->withToken(self::TOKEN)->postJson(route('deploy.finalize'), ['release' => '20261103130000-def5678'])
            ->assertStatus(409)
            ->assertJson(['status' => 'stale', 'release' => self::RELEASE]);
    }

    public function test_it_runs_the_post_deploy_commands(): void
    {
        Artisan::shouldReceive('call')->once()->with('migrate', ['--force' => true])->andReturn(0)->ordered();
        Artisan::shouldReceive('call')->once()->with('storage:link', [])->andReturn(0)->ordered();
        Artisan::shouldReceive('call')->once()->with('optimize', [])->andReturn(0)->ordered();
        Artisan::shouldReceive('output')->andReturn('done');

        $this->withToken(self::TOKEN)->postJson(route('deploy.finalize'), ['release' => self::RELEASE])
            ->assertOk()
            ->assertJson(['status' => 'ok', 'release' => self::RELEASE]);
    }

    public function test_it_stops_at_the_first_failing_command(): void
    {
        Artisan::shouldReceive('call')->once()->with('migrate', ['--force' => true])->andReturn(1);
        Artisan::shouldReceive('call')->with('storage:link', [])->never();
        Artisan::shouldReceive('output')->andReturn('SQLSTATE error');

        $this->withToken(self::TOKEN)->postJson(route('deploy.finalize'), ['release' => self::RELEASE])
            ->assertStatus(500)
            ->assertJson(['status' => 'failed', 'command' => 'migrate']);
        $this->assertSame(0, User::count());
    }

    private function finalize(): TestResponse
    {
        Artisan::shouldReceive('call')->andReturn(0);
        Artisan::shouldReceive('output')->andReturn('done');

        return $this->withToken(self::TOKEN)->postJson(route('deploy.finalize'), ['release' => self::RELEASE]);
    }

    public function test_it_creates_the_admin_account_without_echoing_it(): void
    {
        config(['services.deploy.admin_email' => ' Findel@KPANDJI.com ', 'services.deploy.admin_password' => 'un-mot-de-passe-solide-2026']);

        $response = $this->finalize()->assertOk()->assertJsonPath('output.admin', 'created');

        $this->assertStringNotContainsStringIgnoringCase('findel@kpandji.com', $response->getContent());
        $this->assertStringNotContainsString('un-mot-de-passe-solide-2026', $response->getContent());

        $admin = User::sole();
        $this->assertSame('findel@kpandji.com', $admin->email);
        $this->assertSame('Findel', $admin->name);
        $this->assertTrue($admin->isAdmin());
        $this->assertSame('active', $admin->account_status);
        $this->assertTrue(Hash::check('un-mot-de-passe-solide-2026', $admin->password));

        $this->finalize()->assertJsonPath('output.admin', 'exists');
        $this->assertSame(1, User::count());
    }

    public function test_it_never_changes_an_existing_account(): void
    {
        $existing = User::factory()->client()->create(['email' => 'findel@kpandji.com']);
        config(['services.deploy.admin_email' => 'findel@kpandji.com', 'services.deploy.admin_password' => 'un-autre-mot-de-passe-2026']);

        $this->finalize()->assertOk()->assertJsonPath('output.admin', 'exists');

        $this->assertSame($existing->password, $existing->fresh()->password);
        $this->assertFalse($existing->fresh()->isAdmin());
    }

    public function test_the_admin_needs_both_variables_and_a_long_password(): void
    {
        config(['services.deploy.admin_email' => 'findel@kpandji.com', 'services.deploy.admin_password' => '']);
        $this->finalize()->assertOk()->assertJsonPath('output.admin', 'not-configured');

        config(['services.deploy.admin_password' => 'court']);
        $this->finalize()->assertOk()->assertJsonPath('output.admin', 'password-too-short');

        $this->assertSame(0, User::count());
    }
}
