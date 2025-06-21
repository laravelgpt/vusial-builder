<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ControllerBuilder extends BaseBuilder
{
    public function getDefaultName(): string
    {
        return 'controller';
    }

    public function getConfigPath(): string
    {
        return base_path('config/controller-builder.php');
    }

    public function getStubsPath(): string
    {
        return base_path('stubs/controller-builder');
    }

    public function build(array $config): void
    {
        $this->buildController($config);
    }

    protected function buildController(array $config): void
    {
        $name = $this->getName($config);
        $namespace = $this->getNamespace($config);
        $model = $this->getModel($config);
        $resource = $this->getResource($config);
        $collection = $this->getCollection($config);
        $request = $this->getRequest($config);
        $service = $this->getService($config);
        $repository = $this->getRepository($config);
        $methods = $this->getMethods($config);
        $middleware = $this->getMiddleware($config);
        $authorize = $this->getAuthorize($config);

        $this->createFile(
            $this->getControllerPath($name),
            $this->getStub('controller'),
            [
                'namespace' => $namespace,
                'name' => $name,
                'model' => $model,
                'resource' => $resource,
                'collection' => $collection,
                'request' => $request,
                'service' => $service,
                'repository' => $repository,
                'methods' => $methods,
                'middleware' => $middleware,
                'authorize' => $authorize,
            ]
        );
    }

    protected function getModel(array $config): string
    {
        return $config['model'] ?? '';
    }

    protected function getResource(array $config): string
    {
        return $config['resource'] ?? '';
    }

    protected function getCollection(array $config): string
    {
        return $config['collection'] ?? '';
    }

    protected function getRequest(array $config): string
    {
        return $config['request'] ?? '';
    }

    protected function getService(array $config): string
    {
        return $config['service'] ?? '';
    }

    protected function getRepository(array $config): string
    {
        return $config['repository'] ?? '';
    }

    protected function getMethods(array $config): string
    {
        $methods = $config['methods'] ?? [];
        $result = '';

        foreach ($methods as $method) {
            $result .= $this->getMethod($method);
        }

        return $result;
    }

    protected function getMethod(array $method): string
    {
        $name = $method['name'] ?? '';
        $route = $method['route'] ?? '';
        $method = $method['method'] ?? 'get';
        $middleware = $this->getMethodMiddleware($method);
        $authorize = $this->getMethodAuthorize($method);
        $parameters = $this->getMethodParameters($method);
        $return = $this->getMethodReturn($method);
        $body = $this->getMethodBody($method);

        return <<<PHP
    /**
     * {$name}
     *
     * @return {$return}
     */
    public function {$name}({$parameters})
    {
        {$authorize}
        {$body}
    }

PHP;
    }

    protected function getMethodMiddleware(array $method): string
    {
        $middleware = $method['middleware'] ?? [];
        $result = '';

        foreach ($middleware as $m) {
            $result .= "        \$this->middleware('{$m}');\n";
        }

        return $result;
    }

    protected function getMethodAuthorize(array $method): string
    {
        $authorize = $method['authorize'] ?? '';

        if (empty($authorize)) {
            return '';
        }

        return "        \$this->authorize('{$authorize}');\n";
    }

    protected function getMethodParameters(array $method): string
    {
        $parameters = $method['parameters'] ?? [];
        $result = '';

        foreach ($parameters as $parameter) {
            $type = $parameter['type'] ?? '';
            $name = $parameter['name'] ?? '';
            $default = $parameter['default'] ?? null;

            if (!empty($type)) {
                $result .= "{$type} ";
            }

            $result .= "\${$name}";

            if ($default !== null) {
                $result .= " = {$default}";
            }

            $result .= ', ';
        }

        return rtrim($result, ', ');
    }

    protected function getMethodReturn(array $method): string
    {
        return $method['return'] ?? 'mixed';
    }

    protected function getMethodBody(array $method): string
    {
        $body = $method['body'] ?? '';
        $lines = explode("\n", $body);
        $result = '';

        foreach ($lines as $line) {
            $result .= "        {$line}\n";
        }

        return rtrim($result);
    }

    protected function getMiddleware(array $config): string
    {
        $middleware = $config['middleware'] ?? [];
        $result = '';

        foreach ($middleware as $m) {
            $result .= "        \$this->middleware('{$m}');\n";
        }

        return $result;
    }

    protected function getAuthorize(array $config): string
    {
        $authorize = $config['authorize'] ?? '';

        if (empty($authorize)) {
            return '';
        }

        return "        \$this->authorize('{$authorize}');\n";
    }

    protected function getControllerPath(string $name): string
    {
        return app_path("Http/Controllers/{$name}.php");
    }

    protected function getControllerName(string $model): string
    {
        return $model . 'Controller';
    }

    protected function getStoreRequestName(string $model): string
    {
        return 'Store' . $model . 'Request';
    }

    protected function getUpdateRequestName(string $model): string
    {
        return 'Update' . $model . 'Request';
    }

    protected function getResourceName(string $model): string
    {
        return $model . 'Resource';
    }

    protected function getCollectionName(string $model): string
    {
        return $model . 'Collection';
    }

    protected function getTestName(string $model): string
    {
        return $model . 'ControllerTest';
    }

    protected function getServiceName(string $model): string
    {
        return $model . 'Service';
    }

    protected function getTableName(string $model): string
    {
        return Str::plural(Str::snake($model));
    }

    protected function getRouteName(string $model): string
    {
        return Str::kebab($model);
    }
} 