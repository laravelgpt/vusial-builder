<?php

namespace LaravelBuilder\VisualBuilder\Providers;

use Illuminate\Support\ServiceProvider;
use LaravelBuilder\VisualBuilder\Support\LaravelVersion;
use Laravel\Volt\Volt;

class Laravel12ServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/visual-builder.php', 'visual-builder'
        );

        // Register environment-specific configurations
        $this->registerEnvironmentConfig();
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

        // Register Volt components with enhanced features
        if (LaravelVersion::hasVolt()) {
            $this->registerVoltComponents();
        }

        // Register Livewire components with Prism optimization
        if (class_exists('Livewire\Livewire')) {
            $this->loadLivewireComponents();
        }

        // Register Pennant features with enhanced type declarations
        $this->registerPennantFeatures();

        // Register Reverb with improved WebSocket handling
        $this->registerReverb();

        // Register Prompts with AI enhancements
        if (LaravelVersion::hasPrompts()) {
            $this->registerPrompts();
        }

        // Register modular middleware
        $this->registerMiddleware();
    }

    /**
     * Register environment-specific configurations.
     */
    protected function registerEnvironmentConfig(): void
    {
        $env = $this->app->environment();
        $configPath = __DIR__."/../../config/visual-builder.{$env}.php";

        if (file_exists($configPath)) {
            $this->mergeConfigFrom($configPath, 'visual-builder');
        }
    }

    /**
     * Register Volt components with enhanced features.
     */
    protected function registerVoltComponents(): void
    {
        Volt::component('visual-builder::ai-chat', function () {
            return view('visual-builder::components.ai-chat')
                ->with('features', [
                    'reverb' => true,
                    'pennant' => true,
                    'prism' => true,
                ]);
        });

        Volt::component('visual-builder::error-handler', function () {
            return view('visual-builder::components.error-handler')
                ->with('features', [
                    'reverb' => true,
                    'pennant' => true,
                    'prism' => true,
                ]);
        });
    }

    /**
     * Load Livewire components with Prism optimization.
     */
    protected function loadLivewireComponents(): void
    {
        $this->app->make('livewire')->component('visual-builder::ai-chat', \LaravelBuilder\VisualBuilder\Livewire\AiChat::class);
        $this->app->make('livewire')->component('visual-builder::error-handler', \LaravelBuilder\VisualBuilder\Livewire\ErrorHandler::class);
    }

    /**
     * Register Pennant features with enhanced type declarations.
     */
    protected function registerPennantFeatures(): void
    {
        $pennant = $this->app->make('pennant');

        $pennant->define('ai-chat', function () {
            return true;
        });

        $pennant->define('reverb-support', function () {
            return true;
        });

        $pennant->define('volt-support', function () {
            return LaravelVersion::hasVolt();
        });

        $pennant->define('prism-support', function () {
            return true;
        });
    }

    /**
     * Register Reverb with improved WebSocket handling.
     */
    protected function registerReverb(): void
    {
        $reverb = $this->app->make('reverb');

        $reverb->channel('ai-chat', function () {
            return true;
        });

        $reverb->channel('error-handler', function () {
            return true;
        });

        $reverb->channel('prism-updates', function () {
            return true;
        });
    }

    /**
     * Register Prompts with AI enhancements.
     */
    protected function registerPrompts(): void
    {
        $this->app->make('prompts')->register('ai-chat', function () {
            return new \LaravelBuilder\VisualBuilder\Prompts\AiChatPrompt();
        });

        $this->app->make('prompts')->register('error-handler', function () {
            return new \LaravelBuilder\VisualBuilder\Prompts\ErrorHandlerPrompt();
        });
    }

    /**
     * Register modular middleware.
     */
    protected function registerMiddleware(): void
    {
        $this->app['router']->aliasMiddleware('visual-builder', \LaravelBuilder\VisualBuilder\Middleware\VisualBuilderMiddleware::class);
        $this->app['router']->aliasMiddleware('visual-builder.auth', \LaravelBuilder\VisualBuilder\Middleware\VisualBuilderAuthMiddleware::class);
        $this->app['router']->aliasMiddleware('visual-builder.feature', \LaravelBuilder\VisualBuilder\Middleware\VisualBuilderFeatureMiddleware::class);
    }
} 