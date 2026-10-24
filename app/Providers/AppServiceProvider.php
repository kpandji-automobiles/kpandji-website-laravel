<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (isset($_ENV['VERCEL']) || getenv('VERCEL') === '1') {
            $this->app->useStoragePath('/tmp/storage');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        // Limite les demandes d’essai par IP.
        RateLimiter::for('test-drive', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Investment::class, \App\Policies\InvestmentPolicy::class);

        // CMS : @content('hero.title1') affiche un contenu éditable (échappé)
        \Illuminate\Support\Facades\Blade::directive('content', fn($e) => "<?php echo e(content($e)); ?>");
        \Illuminate\Support\Facades\Blade::directive('cmsEdit', fn($e) => "<?php echo cms_edit($e); ?>");

        // Paramètres promo stockés en base (admin) -> écrasent config/promo.php
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $s = \App\Models\Setting::allCached();
                foreach (['title', 'subtitle', 'reduction_percent', 'ends_at', 'active'] as $k) {
                    if (array_key_exists("promo.$k", $s) && $s["promo.$k"] !== null && $s["promo.$k"] !== '') {
                        config(["promo.$k" => $k === 'reduction_percent' ? (float) $s["promo.$k"] : ($k === 'active' ? (bool) $s["promo.$k"] : $s["promo.$k"])]);
                    } elseif ($k === 'ends_at' && array_key_exists('promo.ends_at', $s)) {
                        config(['promo.ends_at' => null]);
                    }
                }
            }
        } catch (\Throwable $e) { /* base indisponible : on garde config/promo.php */
        }
    }
}
