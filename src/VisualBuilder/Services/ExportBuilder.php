<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ExportBuilder extends BaseBuilder
{
    protected function getDefaultName(): string
    {
        return 'export';
    }

    protected function getConfigPath(): string
    {
        return base_path('config/export-builder.php');
    }

    protected function getStubsPath(): string
    {
        return base_path('stubs/export-builder');
    }

    public function build(array $config): array
    {
        $this->output = [];

        // Create export class
        $this->buildExport($config);

        // Create export view
        $this->buildView($config);

        return $this->output;
    }

    protected function buildExport(array $config): void
    {
        $exportName = $this->getExportName($config['name']);
        $exportPath = app_path("Exports/{$exportName}.php");
        $exportStub = $this->getStub('export');
        $exportContent = $this->replaceStub($exportStub, [
            'namespace' => $this->getNamespace($exportPath),
            'class' => $exportName,
            'model' => $config['name'],
            'modelNamespace' => $this->getNamespace(app_path("Models/{$config['name']}.php")),
            'columns' => $this->getColumns($config),
            'headings' => $this->getHeadings($config),
            'formats' => $this->getFormats($config),
            'styles' => $this->getStyles($config),
            'filters' => $this->getFilters($config),
            'groupBy' => $this->getGroupBy($config),
            'orderBy' => $this->getOrderBy($config),
            'limit' => $config['limit'] ?? null,
        ]);

        $this->createFile($exportPath, $exportContent);
    }

    protected function buildView(array $config): void
    {
        $viewName = $this->getViewName($config['name']);
        $viewPath = resource_path("views/exports/{$viewName}.blade.php");
        $viewStub = $this->getStub('view');
        $viewContent = $this->replaceStub($viewStub, [
            'export' => $this->getExportName($config['name']),
            'model' => $config['name'],
            'modelVariable' => Str::camel(Str::plural($config['name'])),
            'title' => $config['title'] ?? Str::title($config['name']) . ' Export',
            'formats' => $this->getExportFormats($config),
        ]);

        $this->createFile($viewPath, $viewContent);
    }

    protected function getColumns(array $config): string
    {
        $columns = $config['columns'] ?? [];
        $result = [];

        foreach ($columns as $column) {
            $result[] = $this->formatColumn($column);
        }

        return implode(",\n            ", $result);
    }

    protected function formatColumn(array $column): string
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

    protected function getHeadings(array $config): string
    {
        $columns = $config['columns'] ?? [];
        $result = [];

        foreach ($columns as $column) {
            $label = $column['label'] ?? Str::title($column['name']);
            $result[] = "'{$label}'";
        }

        return implode(",\n            ", $result);
    }

    protected function getFormats(array $config): string
    {
        $columns = $config['columns'] ?? [];
        $result = [];

        foreach ($columns as $column) {
            if (isset($column['format'])) {
                $result[] = "'{$column['name']}' => '{$column['format']}'";
            }
        }

        return implode(",\n            ", $result);
    }

    protected function getStyles(array $config): string
    {
        $columns = $config['columns'] ?? [];
        $result = [];

        foreach ($columns as $column) {
            if (isset($column['style'])) {
                $result[] = "'{$column['name']}' => " . json_encode($column['style']);
            }
        }

        return implode(",\n            ", $result);
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

    protected function getGroupBy(array $config): string
    {
        $groupBy = $config['group_by'] ?? [];
        return implode("', '", $groupBy);
    }

    protected function getOrderBy(array $config): string
    {
        $orderBy = $config['order_by'] ?? [];
        $result = [];

        foreach ($orderBy as $column => $direction) {
            $result[] = "'{$column}' => '{$direction}'";
        }

        return implode(",\n            ", $result);
    }

    protected function getExportFormats(array $config): string
    {
        $formats = $config['formats'] ?? ['xlsx', 'csv'];
        $result = [];

        foreach ($formats as $format) {
            $result[] = $this->getExportFormatView($format);
        }

        return implode("\n", $result);
    }

    protected function getExportFormatView(string $format): string
    {
        $label = Str::upper($format);
        $route = "export.{$format}";

        return <<<BLADE
<a href="{{ route('{$route}', request()->query()) }}" class="btn btn-sm btn-secondary">
    <i class="fas fa-file-{$format}"></i> {{ __('Export as {$label}') }}
</a>
BLADE;
    }

    protected function getExportName(string $model): string
    {
        return $model . 'Export';
    }

    protected function getViewName(string $model): string
    {
        return Str::kebab($model) . '-export';
    }

    protected function getRouteName(string $model): string
    {
        return Str::kebab(Str::plural($model));
    }
} 