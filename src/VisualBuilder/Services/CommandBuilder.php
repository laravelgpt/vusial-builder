<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CommandBuilder extends BaseBuilder
{
    protected function getDefaultName(): string
    {
        return 'command';
    }

    protected function getConfigPath(): string
    {
        return base_path('config/command-builder.php');
    }

    protected function getStubsPath(): string
    {
        return base_path('stubs/command-builder');
    }

    public function build(array $config): array
    {
        $this->output = [];

        // Create command class
        $this->buildCommand($config);

        return $this->output;
    }

    protected function buildCommand(array $config): void
    {
        $commandName = $this->getCommandName($config['name']);
        $commandPath = app_path("Console/Commands/{$commandName}.php");
        $commandStub = $this->getStub('command');
        $commandContent = $this->replaceStub($commandStub, [
            'namespace' => $this->getNamespace($commandPath),
            'class' => $commandName,
            'signature' => $this->getSignature($config),
            'description' => $this->getDescription($config),
            'arguments' => $this->getArguments($config),
            'options' => $this->getOptions($config),
            'handle' => $this->getHandle($config),
            'shouldQueue' => $config['should_queue'] ?? false,
            'queue' => $config['queue'] ?? 'default',
            'connection' => $config['connection'] ?? 'sync',
            'tries' => $config['tries'] ?? 3,
            'timeout' => $config['timeout'] ?? 60,
            'retryAfter' => $config['retry_after'] ?? 60,
            'maxExceptions' => $config['max_exceptions'] ?? 3,
        ]);

        $this->createFile($commandPath, $commandContent);
    }

    protected function getSignature(array $config): string
    {
        $signature = $config['signature'] ?? '';
        return $signature;
    }

    protected function getDescription(array $config): string
    {
        $description = $config['description'] ?? '';
        return $description;
    }

    protected function getArguments(array $config): string
    {
        $arguments = $config['arguments'] ?? [];
        $result = [];

        foreach ($arguments as $argument) {
            $result[] = $this->formatArgument($argument);
        }

        return implode("\n        ", $result);
    }

    protected function formatArgument(array $argument): string
    {
        $result = [];
        foreach ($argument as $key => $value) {
            if (is_string($value)) {
                $result[] = "'{$key}' => '{$value}'";
            } else {
                $result[] = "'{$key}' => " . json_encode($value);
            }
        }

        return '[' . implode(', ', $result) . ']';
    }

    protected function getOptions(array $config): string
    {
        $options = $config['options'] ?? [];
        $result = [];

        foreach ($options as $option) {
            $result[] = $this->formatOption($option);
        }

        return implode("\n        ", $result);
    }

    protected function formatOption(array $option): string
    {
        $result = [];
        foreach ($option as $key => $value) {
            if (is_string($value)) {
                $result[] = "'{$key}' => '{$value}'";
            } else {
                $result[] = "'{$key}' => " . json_encode($value);
            }
        }

        return '[' . implode(', ', $result) . ']';
    }

    protected function getHandle(array $config): string
    {
        $handle = $config['handle'] ?? '';
        return $handle;
    }

    protected function getCommandName(string $model): string
    {
        return $model . 'Command';
    }

    protected function getRouteName(string $model): string
    {
        return Str::kebab(Str::plural($model));
    }
} 