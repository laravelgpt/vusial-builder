<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

abstract class BaseBuilder
{
    protected $name;
    protected $config;
    protected $stubs;
    protected $output;
    protected $force;
    protected $verbose;

    public function __construct()
    {
        $this->name = $this->getDefaultName();
        $this->config = $this->loadConfig();
        $this->stubs = $this->loadStubs();
        $this->output = [];
        $this->force = false;
        $this->verbose = false;
    }

    abstract protected function getDefaultName(): string;
    abstract protected function getConfigPath(): string;
    abstract protected function getStubsPath(): string;
    abstract public function build(array $config): array;

    protected function loadConfig(): array
    {
        $configPath = $this->getConfigPath();
        if (!File::exists($configPath)) {
            throw new \RuntimeException("Configuration file not found: {$configPath}");
        }
        return require $configPath;
    }

    protected function loadStubs(): array
    {
        $stubsPath = $this->getStubsPath();
        if (!File::exists($stubsPath)) {
            throw new \RuntimeException("Stubs directory not found: {$stubsPath}");
        }

        $stubs = [];
        foreach (File::files($stubsPath) as $file) {
            $stubs[pathinfo($file, PATHINFO_FILENAME)] = File::get($file);
        }
        return $stubs;
    }

    protected function createFile(string $path, string $content): bool
    {
        if (File::exists($path) && !$this->force) {
            $this->output[] = "File already exists: {$path}";
            return false;
        }

        File::put($path, $content);
        $this->output[] = "Created file: {$path}";
        return true;
    }

    protected function createDirectory(string $path): bool
    {
        if (File::exists($path)) {
            return true;
        }

        File::makeDirectory($path, 0755, true);
        $this->output[] = "Created directory: {$path}";
        return true;
    }

    protected function getStub(string $name): string
    {
        if (!isset($this->stubs[$name])) {
            throw new \RuntimeException("Stub not found: {$name}");
        }
        return $this->stubs[$name];
    }

    protected function replaceStub(string $stub, array $replacements): string
    {
        $content = $stub;
        foreach ($replacements as $key => $value) {
            $content = str_replace("{{ {$key} }}", $value, $content);
        }
        return $content;
    }

    protected function getNamespace(string $path): string
    {
        $baseNamespace = Config::get('visual-builder.namespace', 'App');
        $relativePath = str_replace(base_path(), '', $path);
        $namespace = str_replace('/', '\\', trim($relativePath, '/'));
        return $baseNamespace . '\\' . $namespace;
    }

    protected function getClassName(string $path): string
    {
        return basename($path, '.php');
    }

    protected function getTableName(string $model): string
    {
        return Str::plural(Str::snake($model));
    }

    protected function getRouteName(string $controller): string
    {
        return Str::kebab(str_replace('Controller', '', $controller));
    }

    protected function getViewName(string $controller): string
    {
        return Str::kebab(str_replace('Controller', '', $controller));
    }

    protected function getConfigName(string $name): string
    {
        return Str::kebab($name);
    }

    protected function getMigrationName(string $table): string
    {
        return date('Y_m_d_His') . '_create_' . $table . '_table';
    }

    protected function getSeederName(string $model): string
    {
        return $model . 'Seeder';
    }

    protected function getPolicyName(string $model): string
    {
        return $model . 'Policy';
    }

    protected function getServiceName(string $model): string
    {
        return $model . 'Service';
    }

    protected function getRepositoryName(string $model): string
    {
        return $model . 'Repository';
    }

    protected function getContractName(string $model): string
    {
        return $model . 'Contract';
    }

    protected function getEventName(string $model, string $action): string
    {
        return $model . $action;
    }

    protected function getListenerName(string $model, string $action): string
    {
        return $model . $action . 'Listener';
    }

    protected function getObserverName(string $model): string
    {
        return $model . 'Observer';
    }

    protected function getComponentName(string $name): string
    {
        return Str::studly($name);
    }

    protected function getDirectiveName(string $name): string
    {
        return Str::kebab($name);
    }

    protected function getMiddlewareName(string $name): string
    {
        return Str::kebab($name);
    }

    protected function getCommandName(string $name): string
    {
        return Str::kebab($name);
    }

    protected function getServiceProviderName(string $name): string
    {
        return Str::studly($name) . 'ServiceProvider';
    }

    protected function getWidgetName(string $name): string
    {
        return Str::studly($name);
    }

    protected function getModuleName(string $name): string
    {
        return Str::studly($name);
    }

    protected function getThemeName(string $name): string
    {
        return Str::kebab($name);
    }

    protected function getMenuName(string $name): string
    {
        return Str::kebab($name);
    }

    protected function getFormName(string $name): string
    {
        return Str::kebab($name);
    }

    protected function getApiName(string $name): string
    {
        return Str::kebab($name);
    }

    protected function getAuthName(string $name): string
    {
        return Str::kebab($name);
    }

    protected function getStubName(string $name): string
    {
        return Str::kebab($name);
    }

    protected function getOutput(): array
    {
        return $this->output;
    }

    protected function setForce(bool $force): self
    {
        $this->force = $force;
        return $this;
    }

    protected function setVerbose(bool $verbose): self
    {
        $this->verbose = $verbose;
        return $this;
    }

    protected function isVerbose(): bool
    {
        return $this->verbose;
    }

    protected function isForce(): bool
    {
        return $this->force;
    }

    protected function getName(): string
    {
        return $this->name;
    }

    protected function getConfig(): array
    {
        return $this->config;
    }

    protected function getStubs(): array
    {
        return $this->stubs;
    }
} 