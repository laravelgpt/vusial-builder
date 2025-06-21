<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ResourceBuilder extends BaseBuilder
{
    protected function getDefaultName(): string
    {
        return 'resource';
    }

    protected function getConfigPath(): string
    {
        return base_path('config/resource-builder.php');
    }

    protected function getStubsPath(): string
    {
        return base_path('stubs/resource-builder');
    }

    public function build(array $config): array
    {
        $this->output = [];

        // Create resource class
        $this->buildResource($config);

        // Create collection class
        $this->buildCollection($config);

        return $this->output;
    }

    protected function buildResource(array $config): void
    {
        $resourceName = $this->getResourceName($config['name']);
        $resourcePath = app_path("Http/Resources/{$resourceName}.php");
        $resourceStub = $this->getStub('resource');
        $resourceContent = $this->replaceStub($resourceStub, [
            'namespace' => $this->getNamespace($resourcePath),
            'class' => $resourceName,
            'model' => $config['name'],
            'modelNamespace' => $this->getNamespace(app_path("Models/{$config['name']}.php")),
            'attributes' => $this->getAttributes($config),
            'relationships' => $this->getRelationships($config),
            'additional' => $this->getAdditional($config),
            'with' => $this->getWith($config),
            'when' => $this->getWhen($config),
            'whenLoaded' => $this->getWhenLoaded($config),
            'whenPivotLoaded' => $this->getWhenPivotLoaded($config),
            'whenCounted' => $this->getWhenCounted($config),
            'whenAggregated' => $this->getWhenAggregated($config),
            'whenCondition' => $this->getWhenCondition($config),
            'merge' => $this->getMerge($config),
            'mergeWhen' => $this->getMergeWhen($config),
            'mergeUnless' => $this->getMergeUnless($config),
            'mergeIf' => $this->getMergeIf($config),
            'mergeUnless' => $this->getMergeUnless($config),
        ]);

        $this->createFile($resourcePath, $resourceContent);
    }

    protected function buildCollection(array $config): void
    {
        $collectionName = $this->getCollectionName($config['name']);
        $collectionPath = app_path("Http/Resources/{$collectionName}.php");
        $collectionStub = $this->getStub('collection');
        $collectionContent = $this->replaceStub($collectionStub, [
            'namespace' => $this->getNamespace($collectionPath),
            'class' => $collectionName,
            'resource' => $this->getResourceName($config['name']),
            'resourceNamespace' => $this->getNamespace(app_path("Http/Resources/{$this->getResourceName($config['name'])}.php")),
            'additional' => $this->getAdditional($config),
            'with' => $this->getWith($config),
            'when' => $this->getWhen($config),
            'whenLoaded' => $this->getWhenLoaded($config),
            'whenPivotLoaded' => $this->getWhenPivotLoaded($config),
            'whenCounted' => $this->getWhenCounted($config),
            'whenAggregated' => $this->getWhenAggregated($config),
            'whenCondition' => $this->getWhenCondition($config),
            'merge' => $this->getMerge($config),
            'mergeWhen' => $this->getMergeWhen($config),
            'mergeUnless' => $this->getMergeUnless($config),
            'mergeIf' => $this->getMergeIf($config),
            'mergeUnless' => $this->getMergeUnless($config),
        ]);

        $this->createFile($collectionPath, $collectionContent);
    }

    protected function getAttributes(array $config): string
    {
        $attributes = $config['attributes'] ?? [];
        $result = [];

        foreach ($attributes as $key => $value) {
            if (is_string($value)) {
                $result[] = "'{$key}' => '{$value}'";
            } else {
                $result[] = "'{$key}' => " . json_encode($value);
            }
        }

        return implode(",\n            ", $result);
    }

    protected function getRelationships(array $config): string
    {
        $relationships = $config['relationships'] ?? [];
        $result = [];

        foreach ($relationships as $relationship) {
            $result[] = $this->formatRelationship($relationship);
        }

        return implode(",\n            ", $result);
    }

    protected function formatRelationship(array $relationship): string
    {
        $result = [];
        foreach ($relationship as $key => $value) {
            if (is_string($value)) {
                $result[] = "'{$key}' => '{$value}'";
            } else {
                $result[] = "'{$key}' => " . json_encode($value);
            }
        }

        return '[' . implode(', ', $result) . ']';
    }

    protected function getAdditional(array $config): string
    {
        $additional = $config['additional'] ?? [];
        $result = [];

        foreach ($additional as $key => $value) {
            if (is_string($value)) {
                $result[] = "'{$key}' => '{$value}'";
            } else {
                $result[] = "'{$key}' => " . json_encode($value);
            }
        }

        return implode(",\n            ", $result);
    }

    protected function getWith(array $config): string
    {
        $with = $config['with'] ?? [];
        $result = [];

        foreach ($with as $key => $value) {
            if (is_string($value)) {
                $result[] = "'{$key}' => '{$value}'";
            } else {
                $result[] = "'{$key}' => " . json_encode($value);
            }
        }

        return implode(",\n            ", $result);
    }

    protected function getWhen(array $config): string
    {
        $when = $config['when'] ?? [];
        $result = [];

        foreach ($when as $condition => $value) {
            if (is_string($value)) {
                $result[] = "'{$condition}' => '{$value}'";
            } else {
                $result[] = "'{$condition}' => " . json_encode($value);
            }
        }

        return implode(",\n            ", $result);
    }

    protected function getWhenLoaded(array $config): string
    {
        $whenLoaded = $config['when_loaded'] ?? [];
        $result = [];

        foreach ($whenLoaded as $relationship => $value) {
            if (is_string($value)) {
                $result[] = "'{$relationship}' => '{$value}'";
            } else {
                $result[] = "'{$relationship}' => " . json_encode($value);
            }
        }

        return implode(",\n            ", $result);
    }

    protected function getWhenPivotLoaded(array $config): string
    {
        $whenPivotLoaded = $config['when_pivot_loaded'] ?? [];
        $result = [];

        foreach ($whenPivotLoaded as $relationship => $value) {
            if (is_string($value)) {
                $result[] = "'{$relationship}' => '{$value}'";
            } else {
                $result[] = "'{$relationship}' => " . json_encode($value);
            }
        }

        return implode(",\n            ", $result);
    }

    protected function getWhenCounted(array $config): string
    {
        $whenCounted = $config['when_counted'] ?? [];
        $result = [];

        foreach ($whenCounted as $relationship => $value) {
            if (is_string($value)) {
                $result[] = "'{$relationship}' => '{$value}'";
            } else {
                $result[] = "'{$relationship}' => " . json_encode($value);
            }
        }

        return implode(",\n            ", $result);
    }

    protected function getWhenAggregated(array $config): string
    {
        $whenAggregated = $config['when_aggregated'] ?? [];
        $result = [];

        foreach ($whenAggregated as $relationship => $value) {
            if (is_string($value)) {
                $result[] = "'{$relationship}' => '{$value}'";
            } else {
                $result[] = "'{$relationship}' => " . json_encode($value);
            }
        }

        return implode(",\n            ", $result);
    }

    protected function getWhenCondition(array $config): string
    {
        $whenCondition = $config['when_condition'] ?? [];
        $result = [];

        foreach ($whenCondition as $condition => $value) {
            if (is_string($value)) {
                $result[] = "'{$condition}' => '{$value}'";
            } else {
                $result[] = "'{$condition}' => " . json_encode($value);
            }
        }

        return implode(",\n            ", $result);
    }

    protected function getMerge(array $config): string
    {
        $merge = $config['merge'] ?? [];
        $result = [];

        foreach ($merge as $key => $value) {
            if (is_string($value)) {
                $result[] = "'{$key}' => '{$value}'";
            } else {
                $result[] = "'{$key}' => " . json_encode($value);
            }
        }

        return implode(",\n            ", $result);
    }

    protected function getMergeWhen(array $config): string
    {
        $mergeWhen = $config['merge_when'] ?? [];
        $result = [];

        foreach ($mergeWhen as $condition => $value) {
            if (is_string($value)) {
                $result[] = "'{$condition}' => '{$value}'";
            } else {
                $result[] = "'{$condition}' => " . json_encode($value);
            }
        }

        return implode(",\n            ", $result);
    }

    protected function getMergeUnless(array $config): string
    {
        $mergeUnless = $config['merge_unless'] ?? [];
        $result = [];

        foreach ($mergeUnless as $condition => $value) {
            if (is_string($value)) {
                $result[] = "'{$condition}' => '{$value}'";
            } else {
                $result[] = "'{$condition}' => " . json_encode($value);
            }
        }

        return implode(",\n            ", $result);
    }

    protected function getMergeIf(array $config): string
    {
        $mergeIf = $config['merge_if'] ?? [];
        $result = [];

        foreach ($mergeIf as $condition => $value) {
            if (is_string($value)) {
                $result[] = "'{$condition}' => '{$value}'";
            } else {
                $result[] = "'{$condition}' => " . json_encode($value);
            }
        }

        return implode(",\n            ", $result);
    }

    protected function getResourceName(string $model): string
    {
        return $model . 'Resource';
    }

    protected function getCollectionName(string $model): string
    {
        return $model . 'Collection';
    }

    protected function getRouteName(string $model): string
    {
        return Str::kebab(Str::plural($model));
    }
} 