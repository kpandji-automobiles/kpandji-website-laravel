<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
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
    }
}
