<?php

namespace KyleWLawrence\Cloudflare\Providers;

use Illuminate\Support\ServiceProvider;
use KyleWLawrence\Cloudflare\Services\CloudflareService;
use KyleWLawrence\Cloudflare\Services\NullService;

class CloudflareServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider and merge config.
     *
     * @return void
     */
    public function register()
    {
        $packageName = 'cloudflare-laravel';
        $configPath = __DIR__.'/../../config/cloudflare-laravel.php';

        $this->mergeConfigFrom(
            $configPath, $packageName
        );

        $this->publishes([
            $configPath => config_path(sprintf('%s.php', $packageName)),
        ]);
    }

    /**
     * Bind service to 'Cloudflare' for use with Facade.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind('Cloudflare', function () {
            $driver = config('cloudflare-laravel.driver', 'api');
            if (is_null($driver) || $driver === 'log') {
                return new NullService($driver === 'log');
            }

            return new CloudflareService;
        });
    }
}
