<?php

namespace LaravelBuilder\VisualBuilder\Providers;

use Illuminate\Support\ServiceProvider;
use LaravelBuilder\VisualBuilder\Support\LaravelVersion;

class Laravel10ServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/visual-builder.php', 'visual-builder'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/visual-builder.php' => config_path('visual-builder.php'),
        ], 'visual-builder-config');

        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'visual-builder');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Register Livewire components if available
        if (class_exists('Livewire\Livewire')) {
            $this->loadLivewireComponents();
        }

        // Register Pennant features if available
        if (LaravelVersion::hasPennant()) {
            $this->registerPennantFeatures();
        }

        // Register Reverb if available
        if (LaravelVersion::hasReverb()) {
            $this->registerReverb();
        }
    }

    /**
     * Load Livewire components.
     */
    protected function loadLivewireComponents(): void
    {
        $this->app->make('livewire')->component('visual-builder::ai-chat', \LaravelBuilder\VisualBuilder\Livewire\AiChat::class);
        $this->app->make('livewire')->component('visual-builder::error-handler', \LaravelBuilder\VisualBuilder\Livewire\ErrorHandler::class);
    }

    /**
     * Register Pennant features.
     */
    protected function registerPennantFeatures(): void
    {
        $this->app->make('pennant')->define('ai-chat', function () {
            return true;
        });

        $this->app->make('pennant')->define('reverb-support', function () {
            return LaravelVersion::hasReverb();
        });
    }

    /**
     * Register Reverb.
     */
    protected function registerReverb(): void
    {
        $this->app->make('reverb')->channel('ai-chat', function () {
            return true;
        });
    }
} 