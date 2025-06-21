<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ViewBuilder extends BaseBuilder
{
    protected function getDefaultName(): string
    {
        return 'view';
    }

    protected function getConfigPath(): string
    {
        return base_path('config/view-builder.php');
    }

    protected function getStubsPath(): string
    {
        return base_path('stubs/view-builder');
    }

    public function build(array $config): array
    {
        $this->output = [];

        // Create views
        $this->buildViews($config);

        // Create components
        $this->buildComponents($config);

        // Create layouts
        $this->buildLayouts($config);

        // Create partials
        $this->buildPartials($config);

        return $this->output;
    }

    protected function buildViews(array $config): void
    {
        $views = $config['views'] ?? ['index', 'show', 'create', 'edit'];
        $viewPath = resource_path("views/{$this->getViewPath($config['name'])}");

        foreach ($views as $view) {
            $viewFile = "{$view}.blade.php";
            $viewStub = $this->getStub($view);
            $viewContent = $this->replaceStub($viewStub, [
                'title' => $this->getViewTitle($config['name'], $view),
                'model' => $config['name'],
                'modelVariable' => Str::camel($config['name']),
                'modelPlural' => Str::plural(Str::camel($config['name'])),
                'route' => $this->getRouteName($config['name']),
                'layout' => $config['layout'] ?? 'layouts.app',
                'content' => $this->getViewContent($config, $view),
            ]);

            $this->createFile("{$viewPath}/{$viewFile}", $viewContent);
        }
    }

    protected function buildComponents(array $config): void
    {
        $components = $config['components'] ?? [];
        $componentPath = resource_path("views/components/{$this->getViewPath($config['name'])}");

        foreach ($components as $component) {
            $componentFile = "{$component}.blade.php";
            $componentStub = $this->getStub('component');
            $componentContent = $this->replaceStub($componentStub, [
                'name' => $component,
                'content' => $this->getComponentContent($config, $component),
            ]);

            $this->createFile("{$componentPath}/{$componentFile}", $componentContent);
        }
    }

    protected function buildLayouts(array $config): void
    {
        $layouts = $config['layouts'] ?? [];
        $layoutPath = resource_path('views/layouts');

        foreach ($layouts as $layout) {
            $layoutFile = "{$layout}.blade.php";
            $layoutStub = $this->getStub('layout');
            $layoutContent = $this->replaceStub($layoutStub, [
                'name' => $layout,
                'content' => $this->getLayoutContent($config, $layout),
            ]);

            $this->createFile("{$layoutPath}/{$layoutFile}", $layoutContent);
        }
    }

    protected function buildPartials(array $config): void
    {
        $partials = $config['partials'] ?? [];
        $partialPath = resource_path("views/partials/{$this->getViewPath($config['name'])}");

        foreach ($partials as $partial) {
            $partialFile = "_{$partial}.blade.php";
            $partialStub = $this->getStub('partial');
            $partialContent = $this->replaceStub($partialStub, [
                'name' => $partial,
                'content' => $this->getPartialContent($config, $partial),
            ]);

            $this->createFile("{$partialPath}/{$partialFile}", $partialContent);
        }
    }

    protected function getViewTitle(string $model, string $view): string
    {
        $titles = [
            'index' => Str::plural($model),
            'show' => $model,
            'create' => "Create {$model}",
            'edit' => "Edit {$model}",
        ];

        return $titles[$view] ?? $view;
    }

    protected function getViewContent(array $config, string $view): string
    {
        $contentMap = [
            'index' => $this->getIndexContent($config),
            'show' => $this->getShowContent($config),
            'create' => $this->getCreateContent($config),
            'edit' => $this->getEditContent($config),
        ];

        return $contentMap[$view] ?? '';
    }

    protected function getIndexContent(array $config): string
    {
        $model = $config['name'];
        $modelVariable = Str::camel($model);
        $modelPlural = Str::plural($modelVariable);
        $route = $this->getRouteName($model);

        return <<<BLADE
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('{$modelPlural}') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('{$route}.create') }}" class="btn btn-primary">
                            {{ __('Create') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(count(\${$modelPlural}) > 0)
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    @foreach(\${$modelPlural}->first()->getFillable() as \$field)
                                        <th>{{ __(ucfirst(\$field)) }}</th>
                                    @endforeach
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\${$modelPlural} as \${$modelVariable})
                                    <tr>
                                        @foreach(\${$modelVariable}->getFillable() as \$field)
                                            <td>{{ \${$modelVariable}->{\$field} }}</td>
                                        @endforeach
                                        <td>
                                            <a href="{{ route('{$route}.show', \${$modelVariable}) }}" class="btn btn-info btn-sm">
                                                {{ __('View') }}
                                            </a>
                                            <a href="{{ route('{$route}.edit', \${$modelVariable}) }}" class="btn btn-warning btn-sm">
                                                {{ __('Edit') }}
                                            </a>
                                            <form action="{{ route('{$route}.destroy', \${$modelVariable}) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('Are you sure?') }}')">
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ \${$modelPlural}->links() }}
                    @else
                        <p>{{ __('No {$modelPlural} found.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
BLADE;
    }

    protected function getShowContent(array $config): string
    {
        $model = $config['name'];
        $modelVariable = Str::camel($model);
        $route = $this->getRouteName($model);

        return <<<BLADE
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('{$model}') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('{$route}.index') }}" class="btn btn-secondary">
                            {{ __('Back') }}
                        </a>
                        <a href="{{ route('{$route}.edit', \${$modelVariable}) }}" class="btn btn-warning">
                            {{ __('Edit') }}
                        </a>
                        <form action="{{ route('{$route}.destroy', \${$modelVariable}) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('Are you sure?') }}')">
                                {{ __('Delete') }}
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @foreach(\${$modelVariable}->getFillable() as \$field)
                        <div class="form-group">
                            <label>{{ __(ucfirst(\$field)) }}</label>
                            <p>{{ \${$modelVariable}->{\$field} }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
BLADE;
    }

    protected function getCreateContent(array $config): string
    {
        $model = $config['name'];
        $modelVariable = Str::camel($model);
        $route = $this->getRouteName($model);

        return <<<BLADE
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Create {$model}') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('{$route}.index') }}" class="btn btn-secondary">
                            {{ __('Back') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('{$route}.store') }}" method="POST">
                        @csrf
                        @foreach(\${$modelVariable}->getFillable() as \$field)
                            <div class="form-group">
                                <label for="{\$field}">{{ __(ucfirst(\$field)) }}</label>
                                <input type="text" class="form-control @error(\$field) is-invalid @enderror" id="{\$field}" name="{\$field}" value="{{ old(\$field) }}">
                                @error(\$field)
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ \$message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        @endforeach
                        <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
BLADE;
    }

    protected function getEditContent(array $config): string
    {
        $model = $config['name'];
        $modelVariable = Str::camel($model);
        $route = $this->getRouteName($model);

        return <<<BLADE
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Edit {$model}') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('{$route}.index') }}" class="btn btn-secondary">
                            {{ __('Back') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('{$route}.update', \${$modelVariable}) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @foreach(\${$modelVariable}->getFillable() as \$field)
                            <div class="form-group">
                                <label for="{\$field}">{{ __(ucfirst(\$field)) }}</label>
                                <input type="text" class="form-control @error(\$field) is-invalid @enderror" id="{\$field}" name="{\$field}" value="{{ old(\$field, \${$modelVariable}->{\$field}) }}">
                                @error(\$field)
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ \$message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        @endforeach
                        <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
BLADE;
    }

    protected function getComponentContent(array $config, string $component): string
    {
        return <<<BLADE
<div>
    {{ \$slot }}
</div>
BLADE;
    }

    protected function getLayoutContent(array $config, string $layout): string
    {
        return <<<BLADE
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
    <div class="font-sans text-gray-900 antialiased">
        {{ \$slot }}
    </div>
</body>
</html>
BLADE;
    }

    protected function getPartialContent(array $config, string $partial): string
    {
        return <<<BLADE
<div>
    {{ \$slot }}
</div>
BLADE;
    }

    protected function getViewPath(string $model): string
    {
        return Str::kebab(Str::plural($model));
    }

    protected function getRouteName(string $model): string
    {
        return Str::kebab(Str::plural($model));
    }
} 