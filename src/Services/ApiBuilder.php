<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LaravelBuilder\VisualBuilder\Generators\ControllerGenerator;
use LaravelBuilder\VisualBuilder\Generators\RouteGenerator;
use LaravelBuilder\VisualBuilder\Generators\TransformerGenerator;
use LaravelBuilder\VisualBuilder\Generators\OpenApiGenerator;

class ApiBuilder
{
    protected $controllerGenerator;
    protected $routeGenerator;
    protected $transformerGenerator;
    protected $openApiGenerator;

    public function __construct()
    {
        $this->controllerGenerator = new ControllerGenerator();
        $this->routeGenerator = new RouteGenerator();
        $this->transformerGenerator = new TransformerGenerator();
        $this->openApiGenerator = new OpenApiGenerator();
    }

    public function build(array $config)
    {
        $this->validateConfig($config);

        // Generate API components
        $this->generateControllers($config);
        $this->generateRoutes($config);
        $this->generateTransformers($config);
        $this->generateOpenApiDocs($config);

        return [
            'status' => 'success',
            'message' => 'API built successfully',
            'components' => $this->getGeneratedComponents($config),
        ];
    }

    protected function validateConfig(array $config)
    {
        if (!isset($config['name']) || !isset($config['version'])) {
            throw new \InvalidArgumentException('API name and version are required');
        }
    }

    protected function generateControllers(array $config)
    {
        foreach ($config['endpoints'] as $endpoint) {
            $this->controllerGenerator->generate($endpoint);
        }
    }

    protected function generateRoutes(array $config)
    {
        $this->routeGenerator->generate($config);
    }

    protected function generateTransformers(array $config)
    {
        foreach ($config['endpoints'] as $endpoint) {
            if (isset($endpoint['transformer'])) {
                $this->transformerGenerator->generate($endpoint);
            }
        }
    }

    public function generateOpenApiDocs(array $config = [])
    {
        return $this->openApiGenerator->generate($config);
    }

    public function exportPostmanCollection()
    {
        $collection = [
            'info' => [
                'name' => config('app.name') . ' API',
                'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
            ],
            'item' => [],
        ];

        // Add endpoints to collection
        foreach ($this->getEndpoints() as $endpoint) {
            $collection['item'][] = $this->formatPostmanEndpoint($endpoint);
        }

        return $collection;
    }

    protected function getEndpoints()
    {
        // Get all API routes and format them for Postman
        $routes = collect(\Route::getRoutes())->filter(function ($route) {
            return Str::startsWith($route->uri(), 'api/');
        });

        return $routes->map(function ($route) {
            return [
                'name' => $route->getName(),
                'method' => $route->methods()[0],
                'uri' => $route->uri(),
                'parameters' => $route->parameters(),
            ];
        });
    }

    protected function formatPostmanEndpoint($endpoint)
    {
        return [
            'name' => $endpoint['name'],
            'request' => [
                'method' => $endpoint['method'],
                'url' => [
                    'raw' => '{{base_url}}/' . $endpoint['uri'],
                    'host' => ['{{base_url}}'],
                    'path' => explode('/', $endpoint['uri']),
                ],
            ],
        ];
    }

    protected function getGeneratedComponents(array $config)
    {
        return [
            'controllers' => $this->controllerGenerator->getGeneratedFiles(),
            'routes' => $this->routeGenerator->getGeneratedFiles(),
            'transformers' => $this->transformerGenerator->getGeneratedFiles(),
            'openapi' => $this->openApiGenerator->getGeneratedFiles(),
        ];
    }
} 