<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MenuBuilder extends BaseBuilder
{
    protected function getDefaultName(): string
    {
        return 'menu';
    }

    protected function getConfigPath(): string
    {
        return base_path('config/menu-builder.php');
    }

    protected function getStubsPath(): string
    {
        return base_path('stubs/menu-builder');
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
        $modelName = $config['model'] ?? 'Menu';
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
        $controllerName = $config['controller'] ?? 'MenuController';
        $controllerPath = app_path("Http/Controllers/{$controllerName}.php");
        $controllerStub = $this->getStub('controller');
        $controllerContent = $this->replaceStub($controllerStub, [
            'namespace' => $this->getNamespace($controllerPath),
            'class' => $controllerName,
            'model' => $config['model'] ?? 'Menu',
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
            'title' => $config['title'] ?? 'Menus',
            'model' => $config['model'] ?? 'Menu',
            'route' => $this->getRouteName($config['controller']),
        ]);
        $this->createFile("{$viewPath}/index.blade.php", $indexContent);

        // Create show view
        $showStub = $this->getStub('views/show');
        $showContent = $this->replaceStub($showStub, [
            'title' => $config['title'] ?? 'Menu Details',
            'model' => $config['model'] ?? 'Menu',
            'route' => $this->getRouteName($config['controller']),
        ]);
        $this->createFile("{$viewPath}/show.blade.php", $showContent);

        // Create create view
        $createStub = $this->getStub('views/create');
        $createContent = $this->replaceStub($createStub, [
            'title' => $config['title'] ?? 'Create Menu',
            'model' => $config['model'] ?? 'Menu',
            'route' => $this->getRouteName($config['controller']),
        ]);
        $this->createFile("{$viewPath}/create.blade.php", $createContent);

        // Create edit view
        $editStub = $this->getStub('views/edit');
        $editContent = $this->replaceStub($editStub, [
            'title' => $config['title'] ?? 'Edit Menu',
            'model' => $config['model'] ?? 'Menu',
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
        $tableName = $this->getTableName($config['model'] ?? 'Menu');
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
        $seederName = $this->getSeederName($config['model'] ?? 'Menu');
        $seederPath = database_path("seeders/{$seederName}.php");
        $seederStub = $this->getStub('seeder');
        $seederContent = $this->replaceStub($seederStub, [
            'namespace' => $this->getNamespace($seederPath),
            'class' => $seederName,
            'model' => $config['model'] ?? 'Menu',
            'modelNamespace' => $this->getNamespace(app_path("Models/{$config['model']}.php")),
        ]);

        $this->createFile($seederPath, $seederContent);
    }

    protected function getFillableAttributes(array $config): string
    {
        $attributes = $config['fillable'] ?? [
            'name',
            'slug',
            'description',
            'type',
            'status',
            'parent_id',
            'order',
            'icon',
            'url',
            'target',
            'permission',
        ];

        return "'" . implode("',\n            '", $attributes) . "'";
    }

    protected function getCasts(array $config): string
    {
        $casts = $config['casts'] ?? [
            'status' => 'boolean',
            'order' => 'integer',
            'parent_id' => 'integer',
        ];

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
        $relationships = $config['relationships'] ?? [
            'parent' => [
                'type' => 'belongsTo',
                'model' => 'self',
            ],
            'children' => [
                'type' => 'hasMany',
                'model' => 'self',
            ],
        ];

        $result = [];
        foreach ($relationships as $name => $relationship) {
            $result[] = "public function {$name}()\n    {\n        return \$this->{$relationship['type']}(" .
                "{$relationship['model']}::class);\n    }";
        }

        return implode("\n\n    ", $result);
    }

    protected function getScopes(array $config): string
    {
        $scopes = $config['scopes'] ?? [
            'active' => 'where("status", true)',
            'ordered' => 'orderBy("order", "asc")',
            'root' => 'whereNull("parent_id")',
        ];

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
            '$table->string("name");',
            '$table->string("slug")->unique();',
            '$table->text("description")->nullable();',
            '$table->string("type")->default("default");',
            '$table->boolean("status")->default(true);',
            '$table->foreignId("parent_id")->nullable()->constrained("menus")->onDelete("cascade");',
            '$table->integer("order")->default(0);',
            '$table->string("icon")->nullable();',
            '$table->string("url")->nullable();',
            '$table->string("target")->default("_self");',
            '$table->string("permission")->nullable();',
            '$table->timestamps();',
        ];

        return implode("\n            ", $columns);
    }
} 