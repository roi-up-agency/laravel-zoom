<?php
namespace RoiUp\Zoom;

use Illuminate\Support\ServiceProvider;
use RoiUp\Zoom\Commands\SyncHostsCommand;

class ZoomServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/zoom.php' => config_path('zoom.php'),
            __DIR__.'/views' => resource_path('views/vendor/zoom'),
            __DIR__.'/translations' => resource_path('lang/vendor/zoom'),
        ]);

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/routes/routes.php');
        $this->loadViewsFrom(__DIR__ . '/views', 'zoom');
        $this->loadTranslationsFrom(__DIR__ . '/translations', 'zoom');

        if ($this->app->runningInConsole()) {
            $this->commands([
                SyncHostsCommand::class,
            ]);
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Zoom', function () {
            return new Zoom();
        });

    }

}
