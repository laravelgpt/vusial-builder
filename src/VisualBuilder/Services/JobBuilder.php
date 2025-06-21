<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class JobBuilder extends BaseBuilder
{
    protected function getDefaultName(): string
    {
        return 'job';
    }

    protected function getConfigPath(): string
    {
        return base_path('config/job-builder.php');
    }

    protected function getStubsPath(): string
    {
        return base_path('stubs/job-builder');
    }

    public function build(array $config): array
    {
        $this->output = [];

        // Create job class
        $this->buildJob($config);

        return $this->output;
    }

    protected function buildJob(array $config): void
    {
        $jobName = $this->getJobName($config['name']);
        $jobPath = app_path("Jobs/{$jobName}.php");
        $jobStub = $this->getStub('job');
        $jobContent = $this->replaceStub($jobStub, [
            'namespace' => $this->getNamespace($jobPath),
            'class' => $jobName,
            'model' => $config['name'],
            'modelNamespace' => $this->getNamespace(app_path("Models/{$config['name']}.php")),
            'properties' => $this->getProperties($config),
            'handle' => $this->getHandle($config),
            'queue' => $config['queue'] ?? 'default',
            'connection' => $config['connection'] ?? 'sync',
            'tries' => $config['tries'] ?? 3,
            'timeout' => $config['timeout'] ?? 60,
            'retryAfter' => $config['retry_after'] ?? 60,
            'maxExceptions' => $config['max_exceptions'] ?? 3,
            'backoff' => $this->getBackoff($config),
            'tags' => $this->getTags($config),
            'middleware' => $this->getMiddleware($config),
            'uniqueFor' => $config['unique_for'] ?? 0,
            'uniqueId' => $this->getUniqueId($config),
            'uniqueVia' => $this->getUniqueVia($config),
            'uniqueKey' => $this->getUniqueKey($config),
            'uniqueOn' => $this->getUniqueOn($config),
        ]);

        $this->createFile($jobPath, $jobContent);
    }

    protected function getProperties(array $config): string
    {
        $properties = $config['properties'] ?? [];
        $result = [];

        foreach ($properties as $property) {
            $result[] = $this->formatProperty($property);
        }

        return implode("\n    ", $result);
    }

    protected function formatProperty(array $property): string
    {
        $result = [];
        foreach ($property as $key => $value) {
            if (is_string($value)) {
                $result[] = "'{$key}' => '{$value}'";
            } else {
                $result[] = "'{$key}' => " . json_encode($value);
            }
        }

        return 'public $' . $property['name'] . ';';
    }

    protected function getHandle(array $config): string
    {
        $handle = $config['handle'] ?? '';
        return $handle;
    }

    protected function getBackoff(array $config): string
    {
        $backoff = $config['backoff'] ?? [];
        $result = [];

        foreach ($backoff as $seconds) {
            $result[] = $seconds;
        }

        return implode(', ', $result);
    }

    protected function getTags(array $config): string
    {
        $tags = $config['tags'] ?? [];
        $result = [];

        foreach ($tags as $tag) {
            $result[] = "'{$tag}'";
        }

        return implode(', ', $result);
    }

    protected function getMiddleware(array $config): string
    {
        $middleware = $config['middleware'] ?? [];
        $result = [];

        foreach ($middleware as $m) {
            $result[] = "'{$m}'";
        }

        return implode(', ', $result);
    }

    protected function getUniqueId(array $config): string
    {
        $uniqueId = $config['unique_id'] ?? '';
        return $uniqueId;
    }

    protected function getUniqueVia(array $config): string
    {
        $uniqueVia = $config['unique_via'] ?? '';
        return $uniqueVia;
    }

    protected function getUniqueKey(array $config): string
    {
        $uniqueKey = $config['unique_key'] ?? '';
        return $uniqueKey;
    }

    protected function getUniqueOn(array $config): string
    {
        $uniqueOn = $config['unique_on'] ?? '';
        return $uniqueOn;
    }

    protected function getJobName(string $model): string
    {
        return $model . 'Job';
    }

    protected function getRouteName(string $model): string
    {
        return Str::kebab(Str::plural($model));
    }
} 