<?php

namespace Tests\Feature;

use Illuminate\Cache\RateLimiter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateLimitPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_preserves_attempts_when_cache_services_restart(): void
    {
        // Cache général utilisé sur Vercel.
        config(['cache.default' => 'array']);

        $key = 'test-login-persistence';
        $limiter = $this->freshLimiter();

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $limiter->hit($key, 60);
        }

        $this->assertTrue($limiter->tooManyAttempts($key, 5));

        // Recréation des services sans conserver leur mémoire.
        $restartedLimiter = $this->freshLimiter();

        $this->assertTrue(
            $restartedLimiter->tooManyAttempts($key, 5),
            'Les tentatives précédentes ont été oubliées.'
        );
    }

    /** Recrée le cache et le limiteur. */
    private function freshLimiter(): RateLimiter
    {
        $this->app->forgetInstance('cache');
        $this->app->forgetInstance(RateLimiter::class);

        return $this->app->make(RateLimiter::class);
    }
}