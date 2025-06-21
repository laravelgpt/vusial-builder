<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModelBuilder extends BaseBuilder
{
    protected function getDefaultName(): string
    {
        return 'model';
    }

    protected function getConfigPath(): string
    {
        return base_path('config/model-builder.php');
    }

    protected function getStubsPath(): string
    {
        return base_path('stubs/model-builder');
    }

    public function build(array $config): array
    {
        $this->output = [];

        // Create model
        $this->buildModel($config);

        // Create migration
        $this->buildMigration($config);

        // Create seeder
        $this->buildSeeder($config);

        // Create factory
        $this->buildFactory($config);

        // Create policy
        $this->buildPolicy($config);

        // Create observer
        $this->buildObserver($config);

        // Create service
        $this->buildService($config);

        // Create repository
        $this->buildRepository($config);

        // Create contract
        $this->buildContract($config);

        return $this->output;
    }

    protected function buildModel(array $config): void
    {
        $modelName = $config['name'];
        $modelPath = app_path("Models/{$modelName}.php");
        $modelStub = $this->getStub('model');
        $modelContent = $this->replaceStub($modelStub, [
            'namespace' => $this->getNamespace($modelPath),
            'class' => $modelName,
            'table' => $this->getTableName($modelName),
            'fillable' => $this->getFillableAttributes($config),
            'casts' => $this->getCasts($config),
            'dates' => $this->getDates($config),
            'relationships' => $this->getRelationships($config),
            'scopes' => $this->getScopes($config),
            'accessors' => $this->getAccessors($config),
            'mutators' => $this->getMutators($config),
        ]);

        $this->createFile($modelPath, $modelContent);
    }

    protected function buildMigration(array $config): void
    {
        $tableName = $this->getTableName($config['name']);
        $migrationName = $this->getMigrationName($tableName);
        $migrationPath = database_path("migrations/{$migrationName}.php");
        $migrationStub = $this->getStub('migration');
        $migrationContent = $this->replaceStub($migrationStub, [
            'table' => $tableName,
            'columns' => $this->getMigrationColumns($config),
        ]);

        $this->createFile($migrationPath, $migrationContent);
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

    protected function buildPolicy(array $config): void
    {
        $policyName = $this->getPolicyName($config['name']);
        $policyPath = app_path("Policies/{$policyName}.php");
        $policyStub = $this->getStub('policy');
        $policyContent = $this->replaceStub($policyStub, [
            'namespace' => $this->getNamespace($policyPath),
            'class' => $policyName,
            'model' => $config['name'],
            'modelNamespace' => $this->getNamespace(app_path("Models/{$config['name']}.php")),
        ]);

        $this->createFile($policyPath, $policyContent);
    }

    protected function buildObserver(array $config): void
    {
        $observerName = $this->getObserverName($config['name']);
        $observerPath = app_path("Observers/{$observerName}.php");
        $observerStub = $this->getStub('observer');
        $observerContent = $this->replaceStub($observerStub, [
            'namespace' => $this->getNamespace($observerPath),
            'class' => $observerName,
            'model' => $config['name'],
            'modelNamespace' => $this->getNamespace(app_path("Models/{$config['name']}.php")),
        ]);

        $this->createFile($observerPath, $observerContent);
    }

    protected function buildService(array $config): void
    {
        $serviceName = $this->getServiceName($config['name']);
        $servicePath = app_path("Services/{$serviceName}.php");
        $serviceStub = $this->getStub('service');
        $serviceContent = $this->replaceStub($serviceStub, [
            'namespace' => $this->getNamespace($servicePath),
            'class' => $serviceName,
            'model' => $config['name'],
            'modelNamespace' => $this->getNamespace(app_path("Models/{$config['name']}.php")),
            'repository' => $this->getRepositoryName($config['name']),
            'repositoryNamespace' => $this->getNamespace(app_path("Repositories/{$this->getRepositoryName($config['name'])}.php")),
            'contract' => $this->getContractName($config['name']),
            'contractNamespace' => $this->getNamespace(app_path("Contracts/{$this->getContractName($config['name'])}.php")),
        ]);

        $this->createFile($servicePath, $serviceContent);
    }

    protected function buildRepository(array $config): void
    {
        $repositoryName = $this->getRepositoryName($config['name']);
        $repositoryPath = app_path("Repositories/{$repositoryName}.php");
        $repositoryStub = $this->getStub('repository');
        $repositoryContent = $this->replaceStub($repositoryStub, [
            'namespace' => $this->getNamespace($repositoryPath),
            'class' => $repositoryName,
            'model' => $config['name'],
            'modelNamespace' => $this->getNamespace(app_path("Models/{$config['name']}.php")),
            'contract' => $this->getContractName($config['name']),
            'contractNamespace' => $this->getNamespace(app_path("Contracts/{$this->getContractName($config['name'])}.php")),
        ]);

        $this->createFile($repositoryPath, $repositoryContent);
    }

    protected function buildContract(array $config): void
    {
        $contractName = $this->getContractName($config['name']);
        $contractPath = app_path("Contracts/{$contractName}.php");
        $contractStub = $this->getStub('contract');
        $contractContent = $this->replaceStub($contractStub, [
            'namespace' => $this->getNamespace($contractPath),
            'interface' => $contractName,
        ]);

        $this->createFile($contractPath, $contractContent);
    }

    protected function getFillableAttributes(array $config): string
    {
        $attributes = $config['fillable'] ?? [];

        return "'" . implode("',\n            '", $attributes) . "'";
    }

    protected function getCasts(array $config): string
    {
        $casts = $config['casts'] ?? [];

        $result = [];
        foreach ($casts as $key => $value) {
            $result[] = "'{$key}' => '{$value}'";
        }

        return implode(",\n            ", $result);
    }

    protected function getDates(array $config): string
    {
        $dates = $config['dates'] ?? [];

        return "'" . implode("',\n            '", $dates) . "'";
    }

    protected function getRelationships(array $config): string
    {
        $relationships = $config['relationships'] ?? [];
        $result = [];

        foreach ($relationships as $name => $relationship) {
            $result[] = "public function {$name}()\n    {\n        return \$this->{$relationship['type']}(" .
                "{$relationship['model']}::class);\n    }";
        }

        return implode("\n\n    ", $result);
    }

    protected function getScopes(array $config): string
    {
        $scopes = $config['scopes'] ?? [];
        $result = [];

        foreach ($scopes as $name => $scope) {
            $result[] = "public function scope{$name}(\$query)\n    {\n        return \$query->{$scope};\n    }";
        }

        return implode("\n\n    ", $result);
    }

    protected function getAccessors(array $config): string
    {
        $accessors = $config['accessors'] ?? [];
        $result = [];

        foreach ($accessors as $name => $accessor) {
            $result[] = "public function get{$name}Attribute(\$value)\n    {\n        return {$accessor};\n    }";
        }

        return implode("\n\n    ", $result);
    }

    protected function getMutators(array $config): string
    {
        $mutators = $config['mutators'] ?? [];
        $result = [];

        foreach ($mutators as $name => $mutator) {
            $result[] = "public function set{$name}Attribute(\$value)\n    {\n        \$this->attributes['{$name}'] = {$mutator};\n    }";
        }

        return implode("\n\n    ", $result);
    }

    protected function getMigrationColumns(array $config): string
    {
        $columns = $config['columns'] ?? [
            '$table->id();',
            '$table->timestamps();',
        ];

        return implode("\n            ", $columns);
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

    protected function getTableName(string $model): string
    {
        return Str::plural(Str::snake($model));
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

    protected function getObserverName(string $model): string
    {
        return $model . 'Observer';
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
} 