<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DashboardBuilder extends BaseBuilder
{
    protected function getDefaultName(): string
    {
        return 'dashboard';
    }

    protected function getConfigPath(): string
    {
        return base_path('config/dashboard-builder.php');
    }

    protected function getStubsPath(): string
    {
        return base_path('stubs/dashboard-builder');
    }

    public function build(array $config): array
    {
        $this->output = [];

        // Create dashboard component
        $this->buildComponent($config);

        // Create dashboard view
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
            'widgets' => $this->getWidgets($config),
        ]);

        $this->createFile($componentPath, $componentContent);

        // Create component view
        $viewName = Str::kebab($componentName);
        $viewPath = resource_path("views/components/{$viewName}.blade.php");
        $viewStub = $this->getStub('component_view');
        $viewContent = $this->replaceStub($viewStub, [
            'widgets' => $this->getWidgetsView($config),
        ]);

        $this->createFile($viewPath, $viewContent);
    }

    protected function buildView(array $config): void
    {
        $viewName = $this->getViewName($config['name']);
        $viewPath = resource_path("views/dashboards/{$viewName}.blade.php");
        $viewStub = $this->getStub('view');
        $viewContent = $this->replaceStub($viewStub, [
            'component' => $this->getComponentName($config['name']),
            'title' => $config['title'] ?? 'Dashboard',
            'widgets' => $this->getWidgetsView($config),
        ]);

        $this->createFile($viewPath, $viewContent);
    }

    protected function getWidgets(array $config): string
    {
        $widgets = $config['widgets'] ?? [];
        $result = [];

        foreach ($widgets as $widget) {
            $result[] = $this->formatWidget($widget);
        }

        return implode(",\n            ", $result);
    }

    protected function formatWidget(array $widget): string
    {
        $result = [];
        foreach ($widget as $key => $value) {
            if (is_string($value)) {
                $result[] = "'{$key}' => '{$value}'";
            } else {
                $result[] = "'{$key}' => " . json_encode($value);
            }
        }

        return '[' . implode(', ', $result) . ']';
    }

    protected function getWidgetsView(array $config): string
    {
        $widgets = $config['widgets'] ?? [];
        $result = [];

        foreach ($widgets as $widget) {
            $result[] = $this->getWidgetView($widget);
        }

        return implode("\n\n", $result);
    }

    protected function getWidgetView(array $widget): string
    {
        $type = $widget['type'] ?? 'card';
        $title = $widget['title'] ?? '';
        $content = $widget['content'] ?? '';
        $icon = $widget['icon'] ?? '';
        $color = $widget['color'] ?? 'primary';
        $size = $widget['size'] ?? 'col-md-4';
        $refresh = $widget['refresh'] ?? false;
        $refreshInterval = $widget['refresh_interval'] ?? 60;

        switch ($type) {
            case 'chart':
                return <<<BLADE
<div class="{$size}">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                @if('{$icon}')
                    <i class="{$icon}"></i>
                @endif
                {{ __('{$title}') }}
            </h3>
            @if({$refresh})
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="refresh" data-refresh-interval="{$refreshInterval}">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            @endif
        </div>
        <div class="card-body">
            {!! {$content} !!}
        </div>
    </div>
</div>
BLADE;

            case 'stats':
                return <<<BLADE
<div class="{$size}">
    <div class="small-box bg-{$color}">
        <div class="inner">
            {!! {$content} !!}
        </div>
        @if('{$icon}')
            <div class="icon">
                <i class="{$icon}"></i>
            </div>
        @endif
        <a href="#" class="small-box-footer">
            {{ __('More info') }} <i class="fas fa-arrow-circle-right"></i>
        </a>
    </div>
</div>
BLADE;

            case 'table':
                return <<<BLADE
<div class="{$size}">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                @if('{$icon}')
                    <i class="{$icon}"></i>
                @endif
                {{ __('{$title}') }}
            </h3>
            @if({$refresh})
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="refresh" data-refresh-interval="{$refreshInterval}">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            @endif
        </div>
        <div class="card-body table-responsive p-0">
            {!! {$content} !!}
        </div>
    </div>
</div>
BLADE;

            default:
                return <<<BLADE
<div class="{$size}">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                @if('{$icon}')
                    <i class="{$icon}"></i>
                @endif
                {{ __('{$title}') }}
            </h3>
            @if({$refresh})
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="refresh" data-refresh-interval="{$refreshInterval}">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            @endif
        </div>
        <div class="card-body">
            {!! {$content} !!}
        </div>
    </div>
</div>
BLADE;
        }
    }

    protected function getComponentName(string $model): string
    {
        return $model . 'Dashboard';
    }

    protected function getViewName(string $model): string
    {
        return Str::kebab($model) . '-dashboard';
    }

    protected function getRouteName(string $model): string
    {
        return Str::kebab(Str::plural($model));
    }
} 