<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;

abstract class BaseBuilderServiceProvider extends ServiceProvider
{
    protected $builderName;
    protected $builderConfig;
    protected $builderViews;
    protected $builderRoutes;
    protected $builderMigrations;
    protected $builderAssets;
    protected $builderCommands = [];
    protected $builderPolicies = [];
    protected $builderEvents = [];
    protected $builderObservers = [];
    protected $builderComponents = [];
    protected $builderMiddleware = [];
    protected $builderBindings = [];
    protected $builderSingletons = [];
    protected $builderAliases = [];
    protected $builderProviders = [];

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . "/../../config/{$this->builderConfig}.php",
            $this->builderConfig
        );

        $this->registerBindings();
        $this->registerSingletons();
        $this->registerAliases();
        $this->registerProviders();
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . "/../../resources/views/{$this->builderViews}", $this->builderViews);
        $this->loadRoutesFrom(__DIR__ . "/../../routes/{$this->builderRoutes}.php");
        $this->loadMigrationsFrom(__DIR__ . "/../../database/migrations/{$this->builderMigrations}");
        $this->loadTranslationsFrom(__DIR__ . "/../../resources/lang/{$this->builderName}", $this->builderName);

        $this->publishConfig();
        $this->publishViews();
        $this->publishAssets();
        $this->publishMigrations();
        $this->publishTranslations();

        $this->registerCommands();
        $this->registerPolicies();
        $this->registerEvents();
        $this->registerObservers();
        $this->registerComponents();
        $this->registerDirectives();
        $this->registerMiddleware();
    }

    protected function publishConfig()
    {
        $this->publishes([
            __DIR__ . "/../../config/{$this->builderConfig}.php" => config_path("{$this->builderConfig}.php"),
        ], 'config');
    }

    protected function publishViews()
    {
        $this->publishes([
            __DIR__ . "/../../resources/views/{$this->builderViews}" => resource_path("views/vendor/{$this->builderViews}"),
        ], 'views');
    }

    protected function publishAssets()
    {
        $this->publishes([
            __DIR__ . "/../../resources/{$this->builderAssets}" => public_path("vendor/{$this->builderAssets}"),
        ], 'assets');
    }

    protected function publishMigrations()
    {
        $this->publishes([
            __DIR__ . "/../../database/migrations/{$this->builderMigrations}" => database_path("migrations"),
        ], 'migrations');
    }

    protected function publishTranslations()
    {
        $this->publishes([
            __DIR__ . "/../../resources/lang/{$this->builderName}" => resource_path("lang/vendor/{$this->builderName}"),
        ], 'translations');
    }

    protected function registerCommands()
    {
        if ($this->app->runningInConsole() && !empty($this->builderCommands)) {
            $this->commands($this->builderCommands);
        }
    }

    protected function registerPolicies()
    {
        if (!empty($this->builderPolicies)) {
            foreach ($this->builderPolicies as $model => $policy) {
                Gate::policy($model, $policy);
            }
        }
    }

    protected function registerEvents()
    {
        if (!empty($this->builderEvents)) {
            foreach ($this->builderEvents as $event => $listeners) {
                Event::listen($event, $listeners);
            }
        }
    }

    protected function registerObservers()
    {
        if (!empty($this->builderObservers)) {
            foreach ($this->builderObservers as $model => $observer) {
                $model::observe($observer);
            }
        }
    }

    protected function registerComponents()
    {
        if (!empty($this->builderComponents)) {
            foreach ($this->builderComponents as $name => $component) {
                Blade::component($name, $component);
            }
        }
    }

    protected function registerDirectives()
    {
        // Override this method in child classes to register custom directives
    }

    protected function registerMiddleware()
    {
        if (!empty($this->builderMiddleware)) {
            foreach ($this->builderMiddleware as $name => $middleware) {
                $this->app['router']->aliasMiddleware($name, $middleware);
            }
        }
    }

    protected function registerBindings()
    {
        if (!empty($this->builderBindings)) {
            foreach ($this->builderBindings as $abstract => $concrete) {
                $this->app->bind($abstract, $concrete);
            }
        }
    }

    protected function registerSingletons()
    {
        if (!empty($this->builderSingletons)) {
            foreach ($this->builderSingletons as $abstract => $concrete) {
                $this->app->singleton($abstract, $concrete);
            }
        }
    }

    protected function registerAliases()
    {
        if (!empty($this->builderAliases)) {
            foreach ($this->builderAliases as $alias => $abstract) {
                $this->app->alias($abstract, $alias);
            }
        }
    }

    protected function registerProviders()
    {
        if (!empty($this->builderProviders)) {
            foreach ($this->builderProviders as $provider) {
                $this->app->register($provider);
            }
        }
    }
} 