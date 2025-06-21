<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ChartBuilder extends BaseBuilder
{
    protected function getDefaultName(): string
    {
        return 'chart';
    }

    protected function getConfigPath(): string
    {
        return base_path('config/chart-builder.php');
    }

    protected function getStubsPath(): string
    {
        return base_path('stubs/chart-builder');
    }

    public function build(array $config): array
    {
        $this->output = [];

        // Create chart component
        $this->buildComponent($config);

        // Create chart view
        $this->buildView($config);

        return $this->output;
    }

    protected function buildComponent(array $config): void
    {
        $componentName = $this->getComponentName($config['name']);
        $componentPath = app_path("View/Components/{$componentName}.php");
        $componentStub = $this->getStub('component');
        $componentContent = $this->replaceStub($componentStub, [
            'namespace' => $this->getNamespace($componentPath),
            'class' => $componentName,
            'model' => $config['name'],
            'modelNamespace' => $this->getNamespace(app_path("Models/{$config['name']}.php")),
            'type' => $config['type'] ?? 'line',
            'options' => $this->getChartOptions($config),
            'data' => $this->getChartData($config),
        ]);

        $this->createFile($componentPath, $componentContent);

        // Create component view
        $viewName = Str::kebab($componentName);
        $viewPath = resource_path("views/components/{$viewName}.blade.php");
        $viewStub = $this->getStub('component_view');
        $viewContent = $this->replaceStub($viewStub, [
            'id' => Str::kebab($config['name']) . '-chart',
            'type' => $config['type'] ?? 'line',
            'options' => $this->getChartOptionsJson($config),
            'data' => $this->getChartDataJson($config),
        ]);

        $this->createFile($viewPath, $viewContent);
    }

    protected function buildView(array $config): void
    {
        $viewName = $this->getViewName($config['name']);
        $viewPath = resource_path("views/charts/{$viewName}.blade.php");
        $viewStub = $this->getStub('view');
        $viewContent = $this->replaceStub($viewStub, [
            'component' => $this->getComponentName($config['name']),
            'model' => $config['name'],
            'modelVariable' => Str::camel(Str::plural($config['name'])),
            'title' => $config['title'] ?? Str::title($config['name']),
            'id' => Str::kebab($config['name']) . '-chart',
            'type' => $config['type'] ?? 'line',
            'options' => $this->getChartOptionsJson($config),
            'data' => $this->getChartDataJson($config),
        ]);

        $this->createFile($viewPath, $viewContent);
    }

    protected function getChartOptions(array $config): string
    {
        $options = $config['options'] ?? [];
        $result = [];

        foreach ($options as $key => $value) {
            if (is_string($value)) {
                $result[] = "'{$key}' => '{$value}'";
            } else {
                $result[] = "'{$key}' => " . json_encode($value);
            }
        }

        return implode(",\n            ", $result);
    }

    protected function getChartData(array $config): string
    {
        $data = $config['data'] ?? [];
        $result = [];

        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $result[] = "'{$key}' => '{$value}'";
            } else {
                $result[] = "'{$key}' => " . json_encode($value);
            }
        }

        return implode(",\n            ", $result);
    }

    protected function getChartOptionsJson(array $config): string
    {
        $options = $config['options'] ?? [];
        return json_encode($options, JSON_PRETTY_PRINT);
    }

    protected function getChartDataJson(array $config): string
    {
        $data = $config['data'] ?? [];
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    protected function getComponentName(string $model): string
    {
        return $model . 'Chart';
    }

    protected function getViewName(string $model): string
    {
        return Str::kebab($model) . '-chart';
    }

    protected function getRouteName(string $model): string
    {
        return Str::kebab(Str::plural($model));
    }
} 