<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PageBuilder extends BaseBuilder
{
    protected function getDefaultName(): string
    {
        return 'page';
    }

    protected function getConfigPath(): string
    {
        return base_path('config/page-builder.php');
    }

    protected function getStubsPath(): string
    {
        return base_path('stubs/page-builder');
    }

    public function build(array $config): array
    {
        $this->output = [];

        // Create model
        $this->buildModel($config);

        // Create controller
        $this->buildController($config);

        // Create views
        $this->buildViews($config);

        // Create routes
        $this->buildRoutes($config);

        // Create migration
        $this->buildMigration($config);

        // Create seeder
        $this->buildSeeder($config);

        return $this->output;
    }

    protected function buildModel(array $config): void
    {
        $modelName = $config['model'] ?? 'Page';
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

    protected function buildController(array $config): void
    {
        $controllerName = $config['controller'] ?? 'PageController';
        $controllerPath = app_path("Http/Controllers/{$controllerName}.php");
        $controllerStub = $this->getStub('controller');
        $controllerContent = $this->replaceStub($controllerStub, [
            'namespace' => $this->getNamespace($controllerPath),
            'class' => $controllerName,
            'model' => $config['model'] ?? 'Page',
            'modelNamespace' => $this->getNamespace(app_path("Models/{$config['model']}.php")),
            'view' => $this->getViewName($controllerName),
            'route' => $this->getRouteName($controllerName),
        ]);

        $this->createFile($controllerPath, $controllerContent);
    }

    protected function buildViews(array $config): void
    {
        $viewPath = resource_path("views/{$this->getViewName($config['controller'])}");
        $this->createDirectory($viewPath);

        // Create index view
        $indexStub = $this->getStub('views/index');
        $indexContent = $this->replaceStub($indexStub, [
            'title' => $config['title'] ?? 'Pages',
            'model' => $config['model'] ?? 'Page',
            'route' => $this->getRouteName($config['controller']),
        ]);
        $this->createFile("{$viewPath}/index.blade.php", $indexContent);

        // Create show view
        $showStub = $this->getStub('views/show');
        $showContent = $this->replaceStub($showStub, [
            'title' => $config['title'] ?? 'Page Details',
            'model' => $config['model'] ?? 'Page',
            'route' => $this->getRouteName($config['controller']),
        ]);
        $this->createFile("{$viewPath}/show.blade.php", $showContent);

        // Create create view
        $createStub = $this->getStub('views/create');
        $createContent = $this->replaceStub($createStub, [
            'title' => $config['title'] ?? 'Create Page',
            'model' => $config['model'] ?? 'Page',
            'route' => $this->getRouteName($config['controller']),
        ]);
        $this->createFile("{$viewPath}/create.blade.php", $createContent);

        // Create edit view
        $editStub = $this->getStub('views/edit');
        $editContent = $this->replaceStub($editStub, [
            'title' => $config['title'] ?? 'Edit Page',
            'model' => $config['model'] ?? 'Page',
            'route' => $this->getRouteName($config['controller']),
        ]);
        $this->createFile("{$viewPath}/edit.blade.php", $editContent);
    }

    protected function buildRoutes(array $config): void
    {
        $routePath = base_path("routes/{$this->getRouteName($config['controller'])}.php");
        $routeStub = $this->getStub('routes');
        $routeContent = $this->replaceStub($routeStub, [
            'controller' => $config['controller'],
            'controllerNamespace' => $this->getNamespace(app_path("Http/Controllers/{$config['controller']}.php")),
            'route' => $this->getRouteName($config['controller']),
        ]);

        $this->createFile($routePath, $routeContent);
    }

    protected function buildMigration(array $config): void
    {
        $tableName = $this->getTableName($config['model'] ?? 'Page');
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
        $seederName = $this->getSeederName($config['model'] ?? 'Page');
        $seederPath = database_path("seeders/{$seederName}.php");
        $seederStub = $this->getStub('seeder');
        $seederContent = $this->replaceStub($seederStub, [
            'namespace' => $this->getNamespace($seederPath),
            'class' => $seederName,
            'model' => $config['model'] ?? 'Page',
            'modelNamespace' => $this->getNamespace(app_path("Models/{$config['model']}.php")),
        ]);

        $this->createFile($seederPath, $seederContent);
    }

    protected function getFillableAttributes(array $config): string
    {
        $attributes = $config['fillable'] ?? [
            'title',
            'slug',
            'content',
            'meta_title',
            'meta_description',
            'meta_keywords',
            'status',
            'published_at',
        ];

        return "'" . implode("',\n            '", $attributes) . "'";
    }

    protected function getCasts(array $config): string
    {
        $casts = $config['casts'] ?? [
            'status' => 'boolean',
            'published_at' => 'datetime',
        ];

        $result = [];
        foreach ($casts as $key => $value) {
            $result[] = "'{$key}' => '{$value}'";
        }

        return implode(",\n            ", $result);
    }

    protected function getDates(array $config): string
    {
        $dates = $config['dates'] ?? [
            'published_at',
        ];

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
            '$table->string("title");',
            '$table->string("slug")->unique();',
            '$table->text("content");',
            '$table->string("meta_title")->nullable();',
            '$table->string("meta_description")->nullable();',
            '$table->string("meta_keywords")->nullable();',
            '$table->boolean("status")->default(true);',
            '$table->timestamp("published_at")->nullable();',
            '$table->timestamps();',
        ];

        return implode("\n            ", $columns);
    }
} 