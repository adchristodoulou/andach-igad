<?php
namespace Andach\IGAD;
use Illuminate\Support\ServiceProvider;

class IGADServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('igad', function () {
            return new IGAD(config('services.igad.xboxapikey'));
        });
    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [IGAD::class];
    }
}