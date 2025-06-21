<?php

namespace LaravelBuilder\VisualBuilder\Providers;

use Illuminate\Support\ServiceProvider;
use LaravelBuilder\VisualBuilder\Support\LaravelVersion;
use Laravel\Volt\Volt;

class Laravel11ServiceProvider extends ServiceProvider
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

        // Register Volt components
        if (LaravelVersion::hasVolt()) {
            $this->registerVoltComponents();
        }

        // Register Livewire components if available
        if (class_exists('Livewire\Livewire')) {
            $this->loadLivewireComponents();
        }

        // Register Pennant features
        $this->registerPennantFeatures();

        // Register Reverb
        $this->registerReverb();

        // Register Prompts
        if (LaravelVersion::hasPrompts()) {
            $this->registerPrompts();
        }
    }

    /**
     * Register Volt components.
     */
    protected function registerVoltComponents(): void
    {
        Volt::component('visual-builder::ai-chat', function () {
            return view('visual-builder::components.ai-chat');
        });

        Volt::component('visual-builder::error-handler', function () {
            return view('visual-builder::components.error-handler');
        });
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
            return true;
        });

        $this->app->make('pennant')->define('volt-support', function () {
            return LaravelVersion::hasVolt();
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

        $this->app->make('reverb')->channel('error-handler', function () {
            return true;
        });
    }

    /**
     * Register Prompts.
     */
    protected function registerPrompts(): void
    {
        $this->app->make('prompts')->register('ai-chat', function () {
            return new \LaravelBuilder\VisualBuilder\Prompts\AiChatPrompt();
        });
    }
} 