<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImportBuilder extends BaseBuilder
{
    protected function getDefaultName(): string
    {
        return 'import';
    }

    protected function getConfigPath(): string
    {
        return base_path('config/import-builder.php');
    }

    protected function getStubsPath(): string
    {
        return base_path('stubs/import-builder');
    }

    public function build(array $config): array
    {
        $this->output = [];

        // Create import class
        $this->buildImport($config);

        // Create import view
        $this->buildView($config);

        return $this->output;
    }

    protected function buildImport(array $config): void
    {
        $importName = $this->getImportName($config['name']);
        $importPath = app_path("Imports/{$importName}.php");
        $importStub = $this->getStub('import');
        $importContent = $this->replaceStub($importStub, [
            'namespace' => $this->getNamespace($importPath),
            'class' => $importName,
            'model' => $config['name'],
            'modelNamespace' => $this->getNamespace(app_path("Models/{$config['name']}.php")),
            'columns' => $this->getColumns($config),
            'rules' => $this->getRules($config),
            'messages' => $this->getMessages($config),
            'attributes' => $this->getAttributes($config),
            'batchSize' => $config['batch_size'] ?? 100,
            'chunkSize' => $config['chunk_size'] ?? 1000,
            'uniqueBy' => $this->getUniqueBy($config),
            'updateExisting' => $config['update_existing'] ?? false,
        ]);

        $this->createFile($importPath, $importContent);
    }

    protected function buildView(array $config): void
    {
        $viewName = $this->getViewName($config['name']);
        $viewPath = resource_path("views/imports/{$viewName}.blade.php");
        $viewStub = $this->getStub('view');
        $viewContent = $this->replaceStub($viewStub, [
            'import' => $this->getImportName($config['name']),
            'model' => $config['name'],
            'modelVariable' => Str::camel(Str::plural($config['name'])),
            'title' => $config['title'] ?? Str::title($config['name']) . ' Import',
            'formats' => $this->getImportFormats($config),
            'template' => $this->getTemplateButton($config),
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

    protected function getRules(array $config): string
    {
        $columns = $config['columns'] ?? [];
        $result = [];

        foreach ($columns as $column) {
            if (isset($column['rules'])) {
                $rules = is_array($column['rules']) ? implode("', '", $column['rules']) : $column['rules'];
                $result[] = "'{$column['name']}' => '{$rules}'";
            }
        }

        return implode(",\n            ", $result);
    }

    protected function getMessages(array $config): string
    {
        $columns = $config['columns'] ?? [];
        $result = [];

        foreach ($columns as $column) {
            if (isset($column['messages'])) {
                foreach ($column['messages'] as $rule => $message) {
                    $result[] = "'{$column['name']}.{$rule}' => '{$message}'";
                }
            }
        }

        return implode(",\n            ", $result);
    }

    protected function getAttributes(array $config): string
    {
        $columns = $config['columns'] ?? [];
        $result = [];

        foreach ($columns as $column) {
            if (isset($column['label'])) {
                $result[] = "'{$column['name']}' => '{$column['label']}'";
            }
        }

        return implode(",\n            ", $result);
    }

    protected function getUniqueBy(array $config): string
    {
        $uniqueBy = $config['unique_by'] ?? [];
        return implode("', '", $uniqueBy);
    }

    protected function getImportFormats(array $config): string
    {
        $formats = $config['formats'] ?? ['xlsx', 'csv'];
        $result = [];

        foreach ($formats as $format) {
            $result[] = $this->getImportFormatView($format);
        }

        return implode("\n", $result);
    }

    protected function getImportFormatView(string $format): string
    {
        $label = Str::upper($format);
        $accept = $format === 'xlsx' ? '.xlsx,.xls' : '.csv';

        return <<<BLADE
<div class="form-group">
    <label for="file">{{ __('Import {$label} File') }}</label>
    <div class="custom-file">
        <input type="file" class="custom-file-input" id="file" name="file" accept="{$accept}" required>
        <label class="custom-file-label" for="file">{{ __('Choose file') }}</label>
    </div>
</div>
BLADE;
    }

    protected function getTemplateButton(array $config): string
    {
        $route = "import.template";

        return <<<BLADE
<a href="{{ route('{$route}') }}" class="btn btn-sm btn-secondary">
    <i class="fas fa-file-download"></i> {{ __('Download Template') }}
</a>
BLADE;
    }

    protected function getImportName(string $model): string
    {
        return $model . 'Import';
    }

    protected function getViewName(string $model): string
    {
        return Str::kebab($model) . '-import';
    }

    protected function getRouteName(string $model): string
    {
        return Str::kebab(Str::plural($model));
    }
} 