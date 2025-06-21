<?php

namespace YourPackage\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Illuminate\Support\Facades\View;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/package.php', 'package'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__.'/../../config/package.php' => config_path('package.php'),
        ], 'package-config');

        // Publish views
        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path('views/vendor/package'),
        ], 'package-views');

        // Publish assets
        $this->publishes([
            __DIR__.'/../../resources/assets' => public_path('vendor/package'),
        ], 'package-assets');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'package');

        // Register Blade components
        $this->registerBladeComponents();

        // Register Livewire components
        $this->registerLivewireComponents();

        // Share configuration with views
        View::share('packageConfig', config('package'));
    }

    /**
     * Register Blade components.
     */
    protected function registerBladeComponents(): void
    {
        Blade::componentNamespace('YourPackage\\View\\Components', 'package');
        
        // Register individual components
        Blade::component('package::components.accordion', 'package-accordion');
        Blade::component('package::components.alert', 'package-alert');
        Blade::component('package::components.button', 'package-button');
        Blade::component('package::components.card', 'package-card');
        Blade::component('package::components.form', 'package-form');
        Blade::component('package::components.tabs', 'package-tabs');
        Blade::component('package::components.toast', 'package-toast');
    }

    /**
     * Register Livewire components.
     */
    protected function registerLivewireComponents(): void
    {
        if (class_exists(Livewire::class)) {
            Livewire::component('package.accordion', \YourPackage\Livewire\Components\Accordion::class);
            Livewire::component('package.alert', \YourPackage\Livewire\Components\Alert::class);
            Livewire::component('package.button', \YourPackage\Livewire\Components\Button::class);
            Livewire::component('package.card', \YourPackage\Livewire\Components\Card::class);
            Livewire::component('package.form', \YourPackage\Livewire\Components\Form::class);
            Livewire::component('package.tabs', \YourPackage\Livewire\Components\Tabs::class);
            Livewire::component('package.toast', \YourPackage\Livewire\Components\Toast::class);
        }
    }
} 