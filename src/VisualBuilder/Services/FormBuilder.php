<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FormBuilder extends BaseBuilder
{
    protected function getDefaultName(): string
    {
        return 'form';
    }

    protected function getConfigPath(): string
    {
        return base_path('config/form-builder.php');
    }

    protected function getStubsPath(): string
    {
        return base_path('stubs/form-builder');
    }

    public function build(array $config): array
    {
        $this->output = [];

        // Create form request
        $this->buildRequest($config);

        // Create form component
        $this->buildComponent($config);

        // Create form view
        $this->buildView($config);

        return $this->output;
    }

    protected function buildRequest(array $config): void
    {
        $requestName = $this->getRequestName($config['name']);
        $requestPath = app_path("Http/Requests/{$requestName}.php");
        $requestStub = $this->getStub('request');
        $requestContent = $this->replaceStub($requestStub, [
            'namespace' => $this->getNamespace($requestPath),
            'class' => $requestName,
            'rules' => $this->getValidationRules($config),
            'messages' => $this->getValidationMessages($config),
            'attributes' => $this->getValidationAttributes($config),
        ]);

        $this->createFile($requestPath, $requestContent);
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
            'fields' => $this->getFormFields($config),
        ]);

        $this->createFile($componentPath, $componentContent);

        // Create component view
        $viewName = Str::kebab($componentName);
        $viewPath = resource_path("views/components/{$viewName}.blade.php");
        $viewStub = $this->getStub('component_view');
        $viewContent = $this->replaceStub($viewStub, [
            'fields' => $this->getFormFieldsView($config),
        ]);

        $this->createFile($viewPath, $viewContent);
    }

    protected function buildView(array $config): void
    {
        $viewName = $this->getViewName($config['name']);
        $viewPath = resource_path("views/forms/{$viewName}.blade.php");
        $viewStub = $this->getStub('view');
        $viewContent = $this->replaceStub($viewStub, [
            'component' => $this->getComponentName($config['name']),
            'model' => $config['name'],
            'modelVariable' => Str::camel($config['name']),
            'route' => $this->getRouteName($config['name']),
            'method' => $config['method'] ?? 'POST',
            'fields' => $this->getFormFieldsView($config),
        ]);

        $this->createFile($viewPath, $viewContent);
    }

    protected function getValidationRules(array $config): string
    {
        $rules = $config['rules'] ?? [];
        $result = [];

        foreach ($rules as $field => $rule) {
            if (is_array($rule)) {
                $result[] = "'{$field}' => ['" . implode("', '", $rule) . "']";
            } else {
                $result[] = "'{$field}' => '{$rule}'";
            }
        }

        return implode(",\n            ", $result);
    }

    protected function getValidationMessages(array $config): string
    {
        $messages = $config['messages'] ?? [];
        $result = [];

        foreach ($messages as $field => $message) {
            $result[] = "'{$field}' => '{$message}'";
        }

        return implode(",\n            ", $result);
    }

    protected function getValidationAttributes(array $config): string
    {
        $attributes = $config['attributes'] ?? [];
        $result = [];

        foreach ($attributes as $field => $attribute) {
            $result[] = "'{$field}' => '{$attribute}'";
        }

        return implode(",\n            ", $result);
    }

    protected function getFormFields(array $config): string
    {
        $fields = $config['fields'] ?? [];
        $result = [];

        foreach ($fields as $field) {
            $result[] = $this->formatFormField($field);
        }

        return implode(",\n            ", $result);
    }

    protected function formatFormField(array $field): string
    {
        $result = [];
        foreach ($field as $key => $value) {
            if (is_string($value)) {
                $result[] = "'{$key}' => '{$value}'";
            } else {
                $result[] = "'{$key}' => " . json_encode($value);
            }
        }

        return '[' . implode(', ', $result) . ']';
    }

    protected function getFormFieldsView(array $config): string
    {
        $fields = $config['fields'] ?? [];
        $result = [];

        foreach ($fields as $field) {
            $result[] = $this->getFieldView($field);
        }

        return implode("\n\n", $result);
    }

    protected function getFieldView(array $field): string
    {
        $type = $field['type'] ?? 'text';
        $name = $field['name'];
        $label = $field['label'] ?? Str::title($name);
        $value = $field['value'] ?? "old('{$name}')";
        $required = $field['required'] ?? false;
        $attributes = $field['attributes'] ?? [];

        $attributesString = '';
        foreach ($attributes as $key => $value) {
            $attributesString .= " {$key}=\"{$value}\"";
        }

        if ($required) {
            $attributesString .= ' required';
        }

        switch ($type) {
            case 'textarea':
                return <<<BLADE
<div class="form-group">
    <label for="{$name}">{{ __('{$label}') }}</label>
    <textarea class="form-control @error('{$name}') is-invalid @enderror" id="{$name}" name="{$name}"{$attributesString}>{!! {$value} !!}</textarea>
    @error('{$name}')
        <span class="invalid-feedback" role="alert">
            <strong>{{ \$message }}</strong>
        </span>
    @enderror
</div>
BLADE;

            case 'select':
                $options = $field['options'] ?? [];
                $optionsString = '';
                foreach ($options as $value => $label) {
                    $optionsString .= "<option value=\"{$value}\">{{ __('{$label}') }}</option>\n        ";
                }

                return <<<BLADE
<div class="form-group">
    <label for="{$name}">{{ __('{$label}') }}</label>
    <select class="form-control @error('{$name}') is-invalid @enderror" id="{$name}" name="{$name}"{$attributesString}>
        {$optionsString}
    </select>
    @error('{$name}')
        <span class="invalid-feedback" role="alert">
            <strong>{{ \$message }}</strong>
        </span>
    @enderror
</div>
BLADE;

            case 'checkbox':
                return <<<BLADE
<div class="form-group">
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input @error('{$name}') is-invalid @enderror" id="{$name}" name="{$name}" value="1"{{ {$value} ? ' checked' : '' }}{$attributesString}>
        <label class="custom-control-label" for="{$name}">{{ __('{$label}') }}</label>
        @error('{$name}')
            <span class="invalid-feedback" role="alert">
                <strong>{{ \$message }}</strong>
            </span>
        @enderror
    </div>
</div>
BLADE;

            case 'radio':
                $options = $field['options'] ?? [];
                $optionsString = '';
                foreach ($options as $value => $label) {
                    $optionsString .= <<<BLADE
        <div class="custom-control custom-radio">
            <input type="radio" class="custom-control-input @error('{$name}') is-invalid @enderror" id="{$name}_{$value}" name="{$name}" value="{$value}"{{ {$value} == old('{$name}') ? ' checked' : '' }}{$attributesString}>
            <label class="custom-control-label" for="{$name}_{$value}">{{ __('{$label}') }}</label>
        </div>
BLADE;
                }

                return <<<BLADE
<div class="form-group">
    <label>{{ __('{$label}') }}</label>
    {$optionsString}
    @error('{$name}')
        <span class="invalid-feedback" role="alert">
            <strong>{{ \$message }}</strong>
        </span>
    @enderror
</div>
BLADE;

            default:
                return <<<BLADE
<div class="form-group">
    <label for="{$name}">{{ __('{$label}') }}</label>
    <input type="{$type}" class="form-control @error('{$name}') is-invalid @enderror" id="{$name}" name="{$name}" value="{{ {$value} }}"{$attributesString}>
    @error('{$name}')
        <span class="invalid-feedback" role="alert">
            <strong>{{ \$message }}</strong>
        </span>
    @enderror
</div>
BLADE;
        }
    }

    protected function getRequestName(string $model): string
    {
        return $model . 'FormRequest';
    }

    protected function getComponentName(string $model): string
    {
        return $model . 'Form';
    }

    protected function getViewName(string $model): string
    {
        return Str::kebab($model) . '-form';
    }

    protected function getRouteName(string $model): string
    {
        return Str::kebab(Str::plural($model));
    }
} 