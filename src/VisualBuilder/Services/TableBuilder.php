<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TableBuilder extends BaseBuilder
{
    protected function getDefaultName(): string
    {
        return 'table';
    }

    protected function getConfigPath(): string
    {
        return base_path('config/table-builder.php');
    }

    protected function getStubsPath(): string
    {
        return base_path('stubs/table-builder');
    }

    public function build(array $config): array
    {
        $this->output = [];

        // Create table component
        $this->buildComponent($config);

        // Create table view
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
            'columns' => $this->getTableColumns($config),
            'actions' => $this->getTableActions($config),
        ]);

        $this->createFile($componentPath, $componentContent);

        // Create component view
        $viewName = Str::kebab($componentName);
        $viewPath = resource_path("views/components/{$viewName}.blade.php");
        $viewStub = $this->getStub('component_view');
        $viewContent = $this->replaceStub($viewStub, [
            'columns' => $this->getTableColumnsView($config),
            'actions' => $this->getTableActionsView($config),
        ]);

        $this->createFile($viewPath, $viewContent);
    }

    protected function buildView(array $config): void
    {
        $viewName = $this->getViewName($config['name']);
        $viewPath = resource_path("views/tables/{$viewName}.blade.php");
        $viewStub = $this->getStub('view');
        $viewContent = $this->replaceStub($viewStub, [
            'component' => $this->getComponentName($config['name']),
            'model' => $config['name'],
            'modelVariable' => Str::camel(Str::plural($config['name'])),
            'route' => $this->getRouteName($config['name']),
            'columns' => $this->getTableColumnsView($config),
            'actions' => $this->getTableActionsView($config),
        ]);

        $this->createFile($viewPath, $viewContent);
    }

    protected function getTableColumns(array $config): string
    {
        $columns = $config['columns'] ?? [];
        $result = [];

        foreach ($columns as $column) {
            $result[] = $this->formatTableColumn($column);
        }

        return implode(",\n            ", $result);
    }

    protected function formatTableColumn(array $column): string
    {
        $result = [];
        foreach ($column as $key => $value) {
            if (is_string($value)) {
                $result[] = "'{$key}' => '{$value}'";
            } else {
                $result[] = "'{$key}' => " . json_encode($value);
            }
        }

        return '[' . implode(', ', $result) . ']';
    }

    protected function getTableActions(array $config): string
    {
        $actions = $config['actions'] ?? [];
        $result = [];

        foreach ($actions as $action) {
            $result[] = $this->formatTableAction($action);
        }

        return implode(",\n            ", $result);
    }

    protected function formatTableAction(array $action): string
    {
        $result = [];
        foreach ($action as $key => $value) {
            if (is_string($value)) {
                $result[] = "'{$key}' => '{$value}'";
            } else {
                $result[] = "'{$key}' => " . json_encode($value);
            }
        }

        return '[' . implode(', ', $result) . ']';
    }

    protected function getTableColumnsView(array $config): string
    {
        $columns = $config['columns'] ?? [];
        $result = [];

        foreach ($columns as $column) {
            $result[] = $this->getColumnView($column);
        }

        return implode("\n", $result);
    }

    protected function getColumnView(array $column): string
    {
        $name = $column['name'];
        $label = $column['label'] ?? Str::title($name);
        $sortable = $column['sortable'] ?? false;
        $searchable = $column['searchable'] ?? false;
        $format = $column['format'] ?? null;

        $th = "<th";
        if ($sortable) {
            $th .= " class=\"sortable\" data-sort=\"{$name}\"";
        }
        $th .= ">{{ __('{$label}') }}</th>";

        $td = "<td>";
        if ($format) {
            $td .= "{!! {$format} !!}";
        } else {
            $td .= "{{ \$item->{$name} }}";
        }
        $td .= "</td>";

        return $th . "\n" . $td;
    }

    protected function getTableActionsView(array $config): string
    {
        $actions = $config['actions'] ?? [];
        $result = [];

        foreach ($actions as $action) {
            $result[] = $this->getActionView($action);
        }

        return implode("\n", $result);
    }

    protected function getActionView(array $action): string
    {
        $name = $action['name'];
        $label = $action['label'] ?? Str::title($name);
        $route = $action['route'] ?? $this->getRouteName($action['model'] ?? '');
        $method = $action['method'] ?? 'GET';
        $icon = $action['icon'] ?? '';
        $class = $action['class'] ?? 'btn btn-sm';
        $confirm = $action['confirm'] ?? null;

        $button = "<a href=\"{{ route('{$route}', \$item) }}\" class=\"{$class}\"";
        if ($confirm) {
            $button .= " onclick=\"return confirm('{$confirm}')\"";
        }
        $button .= ">";

        if ($icon) {
            $button .= "<i class=\"{$icon}\"></i> ";
        }

        $button .= "{{ __('{$label}') }}</a>";

        return $button;
    }

    protected function getComponentName(string $model): string
    {
        return $model . 'Table';
    }

    protected function getViewName(string $model): string
    {
        return Str::kebab($model) . '-table';
    }

    protected function getRouteName(string $model): string
    {
        return Str::kebab(Str::plural($model));
    }
} 