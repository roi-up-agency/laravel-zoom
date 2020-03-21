<?php
namespace RoiUp\Zoom;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use RoiUp\Zoom\Listeners\MeetingEventSubscriber;

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
        ]);

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/routes/routes.php');

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
