<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SeederBuilder extends BaseBuilder
{
    protected function getDefaultName(): string
    {
        return 'seeder';
    }

    protected function getConfigPath(): string
    {
        return base_path('config/seeder-builder.php');
    }

    protected function getStubsPath(): string
    {
        return base_path('stubs/seeder-builder');
    }

    public function build(array $config): array
    {
        $this->output = [];

        // Create seeder
        $this->buildSeeder($config);

        // Create factory
        $this->buildFactory($config);

        return $this->output;
    }

    protected function buildSeeder(array $config): void
    {
        $seederName = $this->getSeederName($config['name']);
        $seederPath = database_path("seeders/{$seederName}.php");
        $seederStub = $this->getStub('seeder');
        $seederContent = $this->replaceStub($seederStub, [
            'namespace' => $this->getNamespace($seederPath),
            'class' => $seederName,
            'model' => $config['name'],
            'modelNamespace' => $this->getNamespace(app_path("Models/{$config['name']}.php")),
            'count' => $config['count'] ?? 10,
            'data' => $this->getSeederData($config),
        ]);

        $this->createFile($seederPath, $seederContent);
    }

    protected function buildFactory(array $config): void
    {
        $factoryName = $config['name'] . 'Factory';
        $factoryPath = database_path("factories/{$factoryName}.php");
        $factoryStub = $this->getStub('factory');
        $factoryContent = $this->replaceStub($factoryStub, [
            'namespace' => $this->getNamespace($factoryPath),
            'class' => $factoryName,
            'model' => $config['name'],
            'modelNamespace' => $this->getNamespace(app_path("Models/{$config['name']}.php")),
            'attributes' => $this->getFactoryAttributes($config),
        ]);

        $this->createFile($factoryPath, $factoryContent);
    }

    protected function getSeederData(array $config): string
    {
        $data = $config['data'] ?? [];
        $result = [];

        foreach ($data as $item) {
            $result[] = $this->formatSeederData($item);
        }

        return implode(",\n            ", $result);
    }

    protected function formatSeederData(array $data): string
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $result[] = "'{$key}' => '{$value}'";
            } else {
                $result[] = "'{$key}' => {$value}";
            }
        }

        return '[' . implode(', ', $result) . ']';
    }

    protected function getFactoryAttributes(array $config): string
    {
        $attributes = $config['factory'] ?? [];
        $result = [];

        foreach ($attributes as $key => $value) {
            if (is_string($value)) {
                $result[] = "'{$key}' => '{$value}'";
            } else {
                $result[] = "'{$key}' => {$value}";
            }
        }

        return implode(",\n            ", $result);
    }

    protected function getSeederName(string $model): string
    {
        return $model . 'Seeder';
    }
} 