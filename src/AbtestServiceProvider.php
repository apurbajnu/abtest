<?php

namespace Apurbajnu\Abtest;

use Apurbajnu\Abtest\Commands\ReportCommand;
use Apurbajnu\Abtest\Commands\ResetCommand;
use Apurbajnu\Abtest\Abtest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AbtestServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
         if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('ab-testing.php'),
            ], 'config');

            $this->commands([
                ReportCommand::class,
                ResetCommand::class,
            ]);
        }

        Request::macro('abExperiment', function () {
            return app(Abtest::class)->getExperiment();
        });

        Blade::if('ab', function ($experiment) {
            return app(Abtest::class)->isExperiment($experiment);
        
        });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'ab-testing');

        // Register the main class to use with the facade
        $this->app->singleton('Abtest', function () {
            return new Abtest;
        });
    }
}
