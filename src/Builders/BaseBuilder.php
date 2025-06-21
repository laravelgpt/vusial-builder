<?php

namespace App\Builders;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;

abstract class BaseBuilder
{
    protected $name;
    protected $config;
    protected $stubs;
    protected $output;
    protected $force;
    protected $verbose;

    public function __construct($name = null, $force = false, $verbose = false)
    {
        $this->name = $name ?? $this->getDefaultName();
        $this->force = $force;
        $this->verbose = $verbose;
        $this->config = $this->loadConfig();
        $this->stubs = $this->loadStubs();
    }

    abstract protected function getDefaultName(): string;
    abstract protected function getConfigPath(): string;
    abstract protected function getStubsPath(): string;
    abstract protected function build(): bool;

    protected function loadConfig(): array
    {
        $configPath = $this->getConfigPath();
        return File::exists($configPath) ? require $configPath : [];
    }

    protected function loadStubs(): array
    {
        $stubsPath = $this->getStubsPath();
        $stubs = [];

        if (File::exists($stubsPath)) {
            foreach (File::files($stubsPath) as $file) {
                $stubs[$file->getBasename('.stub')] = File::get($file->getPathname());
            }
        }

        return $stubs;
    }

    protected function getStub(string $name): ?string
    {
        return $this->stubs[$name] ?? null;
    }

    protected function replaceStub(string $stub, array $replacements): string
    {
        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $stub
        );
    }

    protected function createFile(string $path, string $content): bool
    {
        if (File::exists($path) && !$this->force) {
            $this->output("File already exists: {$path}", 'warning');
            return false;
        }

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);

        $this->output("Created file: {$path}", 'info');
        return true;
    }

    protected function createDirectory(string $path): bool
    {
        if (File::exists($path) && !$this->force) {
            $this->output("Directory already exists: {$path}", 'warning');
            return false;
        }

        File::makeDirectory($path, 0755, true);
        $this->output("Created directory: {$path}", 'info');
        return true;
    }

    protected function output(string $message, string $type = 'info'): void
    {
        if ($this->verbose) {
            $this->output->{$type}($message);
        }
    }

    protected function runCommand(string $command, array $options = []): int
    {
        return Artisan::call($command, $options);
    }

    protected function getNamespace(string $path): string
    {
        return str_replace('/', '\\', $path);
    }

    protected function getClassName(string $name): string
    {
        return Str::studly($name);
    }

    protected function getTableName(string $name): string
    {
        return Str::snake(Str::pluralStudly($name));
    }

    protected function getRouteName(string $name): string
    {
        return Str::kebab($name);
    }

    protected function getViewName(string $name): string
    {
        return Str::kebab($name);
    }

    protected function getConfigName(string $name): string
    {
        return Str::kebab($name);
    }

    protected function getMigrationName(string $name): string
    {
        return date('Y_m_d_His') . '_create_' . $this->getTableName($name) . '_table';
    }

    protected function getSeederName(string $name): string
    {
        return $this->getClassName($name) . 'Seeder';
    }

    protected function getFactoryName(string $name): string
    {
        return $this->getClassName($name) . 'Factory';
    }

    protected function getPolicyName(string $name): string
    {
        return $this->getClassName($name) . 'Policy';
    }

    protected function getEventName(string $name): string
    {
        return $this->getClassName($name) . 'Event';
    }

    protected function getListenerName(string $name): string
    {
        return $this->getClassName($name) . 'Listener';
    }

    protected function getObserverName(string $name): string
    {
        return $this->getClassName($name) . 'Observer';
    }

    protected function getCommandName(string $name): string
    {
        return $this->getClassName($name) . 'Command';
    }

    protected function getServiceProviderName(string $name): string
    {
        return $this->getClassName($name) . 'ServiceProvider';
    }

    protected function getMiddlewareName(string $name): string
    {
        return $this->getClassName($name) . 'Middleware';
    }

    protected function getGuardName(string $name): string
    {
        return $this->getClassName($name) . 'Guard';
    }

    protected function getComponentName(string $name): string
    {
        return $this->getClassName($name) . 'Component';
    }

    protected function getDirectiveName(string $name): string
    {
        return $this->getClassName($name) . 'Directive';
    }

    protected function getMacroName(string $name): string
    {
        return $this->getClassName($name) . 'Macro';
    }

    protected function getBindingName(string $name): string
    {
        return $this->getClassName($name) . 'Binding';
    }

    protected function getSingletonName(string $name): string
    {
        return $this->getClassName($name) . 'Singleton';
    }

    protected function getAliasName(string $name): string
    {
        return $this->getClassName($name) . 'Alias';
    }

    protected function getProviderName(string $name): string
    {
        return $this->getClassName($name) . 'Provider';
    }
} 