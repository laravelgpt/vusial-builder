<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RouteBuilder extends BaseBuilder
{
    protected function getDefaultName(): string
    {
        return 'route';
    }

    protected function getConfigPath(): string
    {
        return base_path('config/route-builder.php');
    }

    protected function getStubsPath(): string
    {
        return base_path('stubs/route-builder');
    }

    public function build(array $config): array
    {
        $this->output = [];

        // Create routes file
        $this->buildRoutes($config);

        // Create middleware
        $this->buildMiddleware($config);

        // Create route service provider
        $this->buildServiceProvider($config);

        return $this->output;
    }

    protected function buildRoutes(array $config): void
    {
        $routePath = base_path("routes/{$this->getRouteName($config['name'])}.php");
        $routeStub = $this->getStub('routes');
        $routeContent = $this->replaceStub($routeStub, [
            'name' => $config['name'],
            'prefix' => $config['prefix'] ?? '',
            'middleware' => $this->getMiddlewareString($config['middleware'] ?? []),
            'namespace' => $config['namespace'] ?? '',
            'routes' => $this->getRoutesString($config['routes'] ?? []),
        ]);

        $this->createFile($routePath, $routeContent);
    }

    protected function buildMiddleware(array $config): void
    {
        if (empty($config['middleware'])) {
            return;
        }

        foreach ($config['middleware'] as $name => $middleware) {
            $middlewarePath = app_path("Http/Middleware/{$name}.php");
            $middlewareStub = $this->getStub('middleware');
            $middlewareContent = $this->replaceStub($middlewareStub, [
                'namespace' => $this->getNamespace($middlewarePath),
                'class' => $name,
            ]);

            $this->createFile($middlewarePath, $middlewareContent);
        }
    }

    protected function buildServiceProvider(array $config): void
    {
        $providerName = $this->getServiceProviderName($config['name']);
        $providerPath = app_path("Providers/{$providerName}.php");
        $providerStub = $this->getStub('service-provider');
        $providerContent = $this->replaceStub($providerStub, [
            'namespace' => $this->getNamespace($providerPath),
            'class' => $providerName,
            'name' => $config['name'],
            'route' => $this->getRouteName($config['name']),
        ]);

        $this->createFile($providerPath, $providerContent);
    }

    protected function getMiddlewareString(array $middleware): string
    {
        if (empty($middleware)) {
            return '';
        }

        $result = [];
        foreach ($middleware as $name => $config) {
            if (is_string($config)) {
                $result[] = "'{$config}'";
            } else {
                $result[] = "'{$name}'";
            }
        }

        return implode(', ', $result);
    }

    protected function getRoutesString(array $routes): string
    {
        if (empty($routes)) {
            return '';
        }

        $result = [];
        foreach ($routes as $route) {
            $method = $route['method'] ?? 'get';
            $uri = $route['uri'] ?? '';
            $action = $route['action'] ?? '';
            $name = $route['name'] ?? '';
            $middleware = $this->getMiddlewareString($route['middleware'] ?? []);

            $routeString = "Route::{$method}('{$uri}', {$action})";
            
            if (!empty($name)) {
                $routeString .= "->name('{$name}')";
            }

            if (!empty($middleware)) {
                $routeString .= "->middleware([{$middleware}])";
            }

            $result[] = $routeString . ';';
        }

        return implode("\n        ", $result);
    }

    protected function getRouteName(string $name): string
    {
        return Str::kebab($name);
    }

    protected function getServiceProviderName(string $name): string
    {
        return Str::studly($name) . 'ServiceProvider';
    }
} 