<?php

namespace PHPJuice\Blueprint;

use Illuminate\Support\ServiceProvider;

class BlueprintServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/blueprint.php', 'blueprint');

        // Register the service the package provides.
        $this->app->singleton('blueprint', function ($app) {
            return new Blueprint();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['blueprint'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/blueprint.php' => config_path('blueprint.php'),
        ], 'blueprint.config');

        // Publishing templates.
        $this->publishes([
            __DIR__.'/Stubs' => base_path('resources/vendor/blueprint'),
        ], 'blueprint.templates');

        // Registering package commands.
        $this->commands([
            Commands\BlueprintCommand::class,
            Commands\BlueprintMakeCommand::class,
            Commands\BlueprintTestCommand::class,
            Commands\BlueprintModelCommand::class,
            Commands\BlueprintRequestCommand::class,
            Commands\BlueprintResourceCommand::class,
            Commands\BlueprintGenerateCommand::class,
            Commands\BlueprintMigrationCommand::class,
            Commands\BlueprintMigrationFkCommand::class,
            Commands\BlueprintControllerApiCommand::class,
        ]);
    }
}
