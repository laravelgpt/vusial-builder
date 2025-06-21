<?php

namespace LaravelBuilder\VisualBuilder;

use Illuminate\Support\ServiceProvider;
use LaravelBuilder\VisualBuilder\Console\Commands\InstallCommand;
use LaravelBuilder\VisualBuilder\Console\Commands\BuildApiCommand;
use LaravelBuilder\VisualBuilder\Console\Commands\BuildAuthCommand;

class VisualBuilderServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/visual-builder.php', 'visual-builder'
        );

        $this->app->singleton('visual-builder', function ($app) {
            return new VisualBuilder($app);
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                BuildApiCommand::class,
                BuildAuthCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/visual-builder.php' => config_path('visual-builder.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/visual-builder'),
            ], 'views');

            $this->publishes([
                __DIR__.'/../resources/js' => resource_path('js/vendor/visual-builder'),
            ], 'assets');

            $this->publishes([
                __DIR__.'/../database/seeders/VisualBuilderSeeder.php' => database_path('seeders/VisualBuilderSeeder.php'),
            ], 'seeders');

            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'visual-builder');
    }
} 