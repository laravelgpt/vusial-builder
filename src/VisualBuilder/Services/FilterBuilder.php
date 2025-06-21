<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FilterBuilder extends BaseBuilder
{
    protected function getDefaultName(): string
    {
        return 'filter';
    }

    protected function getConfigPath(): string
    {
        return base_path('config/filter-builder.php');
    }

    protected function getStubsPath(): string
    {
        return base_path('stubs/filter-builder');
    }

    public function build(array $config): array
    {
        $this->output = [];

        // Create filter component
        $this->buildComponent($config);

        // Create filter view
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
            'filters' => $this->getFilters($config),
        ]);

        $this->createFile($componentPath, $componentContent);

        // Create component view
        $viewName = Str::kebab($componentName);
        $viewPath = resource_path("views/components/{$viewName}.blade.php");
        $viewStub = $this->getStub('component_view');
        $viewContent = $this->replaceStub($viewStub, [
            'filters' => $this->getFiltersView($config),
        ]);

        $this->createFile($viewPath, $viewContent);
    }

    protected function buildView(array $config): void
    {
        $viewName = $this->getViewName($config['name']);
        $viewPath = resource_path("views/filters/{$viewName}.blade.php");
        $viewStub = $this->getStub('view');
        $viewContent = $this->replaceStub($viewStub, [
            'component' => $this->getComponentName($config['name']),
            'model' => $config['name'],
            'modelVariable' => Str::camel(Str::plural($config['name'])),
            'route' => $this->getRouteName($config['name']),
            'filters' => $this->getFiltersView($config),
        ]);

        $this->createFile($viewPath, $viewContent);
    }

    protected function getFilters(array $config): string
    {
        $filters = $config['filters'] ?? [];
        $result = [];

        foreach ($filters as $filter) {
            $result[] = $this->formatFilter($filter);
        }

        return implode(",\n            ", $result);
    }

    protected function formatFilter(array $filter): string
    {
        $result = [];
        foreach ($filter as $key => $value) {
            if (is_string($value)) {
                $result[] = "'{$key}' => '{$value}'";
            } else {
                $result[] = "'{$key}' => " . json_encode($value);
            }
        }

        return '[' . implode(', ', $result) . ']';
    }

    protected function getFiltersView(array $config): string
    {
        $filters = $config['filters'] ?? [];
        $result = [];

        foreach ($filters as $filter) {
            $result[] = $this->getFilterView($filter);
        }

        return implode("\n\n", $result);
    }

    protected function getFilterView(array $filter): string
    {
        $type = $filter['type'] ?? 'text';
        $name = $filter['name'];
        $label = $filter['label'] ?? Str::title($name);
        $value = $filter['value'] ?? "old('{$name}')";
        $placeholder = $filter['placeholder'] ?? '';
        $attributes = $filter['attributes'] ?? [];

        $attributesString = '';
        foreach ($attributes as $key => $value) {
            $attributesString .= " {$key}=\"{$value}\"";
        }

        if ($placeholder) {
            $attributesString .= " placeholder=\"{$placeholder}\"";
        }

        switch ($type) {
            case 'select':
                $options = $filter['options'] ?? [];
                $optionsString = '';
                foreach ($options as $value => $label) {
                    $optionsString .= "<option value=\"{$value}\">{{ __('{$label}') }}</option>\n        ";
                }

                return <<<BLADE
<div class="form-group">
    <label for="{$name}">{{ __('{$label}') }}</label>
    <select class="form-control" id="{$name}" name="{$name}"{$attributesString}>
        <option value="">{{ __('Select') }}</option>
        {$optionsString}
    </select>
</div>
BLADE;

            case 'date':
                return <<<BLADE
<div class="form-group">
    <label for="{$name}">{{ __('{$label}') }}</label>
    <input type="date" class="form-control" id="{$name}" name="{$name}" value="{{ {$value} }}"{$attributesString}>
</div>
BLADE;

            case 'date_range':
                $startName = "{$name}_start";
                $endName = "{$name}_end";
                $startValue = "old('{$startName}')";
                $endValue = "old('{$endName}')";

                return <<<BLADE
<div class="form-group">
    <label>{{ __('{$label}') }}</label>
    <div class="input-group">
        <input type="date" class="form-control" id="{$startName}" name="{$startName}" value="{{ {$startValue} }}"{$attributesString}>
        <div class="input-group-append input-group-prepend">
            <span class="input-group-text">{{ __('to') }}</span>
        </div>
        <input type="date" class="form-control" id="{$endName}" name="{$endName}" value="{{ {$endValue} }}"{$attributesString}>
    </div>
</div>
BLADE;

            case 'number_range':
                $startName = "{$name}_start";
                $endName = "{$name}_end";
                $startValue = "old('{$startName}')";
                $endValue = "old('{$endName}')";

                return <<<BLADE
<div class="form-group">
    <label>{{ __('{$label}') }}</label>
    <div class="input-group">
        <input type="number" class="form-control" id="{$startName}" name="{$startName}" value="{{ {$startValue} }}"{$attributesString}>
        <div class="input-group-append input-group-prepend">
            <span class="input-group-text">{{ __('to') }}</span>
        </div>
        <input type="number" class="form-control" id="{$endName}" name="{$endName}" value="{{ {$endValue} }}"{$attributesString}>
    </div>
</div>
BLADE;

            case 'checkbox':
                return <<<BLADE
<div class="form-group">
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" id="{$name}" name="{$name}" value="1"{{ {$value} ? ' checked' : '' }}{$attributesString}>
        <label class="custom-control-label" for="{$name}">{{ __('{$label}') }}</label>
    </div>
</div>
BLADE;

            default:
                return <<<BLADE
<div class="form-group">
    <label for="{$name}">{{ __('{$label}') }}</label>
    <input type="{$type}" class="form-control" id="{$name}" name="{$name}" value="{{ {$value} }}"{$attributesString}>
</div>
BLADE;
        }
    }

    protected function getComponentName(string $model): string
    {
        return $model . 'Filter';
    }

    protected function getViewName(string $model): string
    {
        return Str::kebab($model) . '-filter';
    }

    protected function getRouteName(string $model): string
    {
        return Str::kebab(Str::plural($model));
    }
} 