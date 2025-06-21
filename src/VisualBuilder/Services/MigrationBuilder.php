<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MigrationBuilder extends BaseBuilder
{
    protected function getDefaultName(): string
    {
        return 'migration';
    }

    protected function getConfigPath(): string
    {
        return base_path('config/migration-builder.php');
    }

    protected function getStubsPath(): string
    {
        return base_path('stubs/migration-builder');
    }

    public function build(array $config): array
    {
        $this->output = [];

        // Create migration
        $this->buildMigration($config);

        // Create foreign keys
        $this->buildForeignKeys($config);

        // Create indexes
        $this->buildIndexes($config);

        return $this->output;
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

    protected function buildForeignKeys(array $config): void
    {
        $foreignKeys = $config['foreign_keys'] ?? [];
        if (empty($foreignKeys)) {
            return;
        }

        $tableName = $this->getTableName($config['name']);
        $migrationName = $this->getMigrationName($tableName, 'foreign_keys');
        $migrationPath = database_path("migrations/{$migrationName}.php");
        $migrationStub = $this->getStub('foreign_keys');
        $migrationContent = $this->replaceStub($migrationStub, [
            'table' => $tableName,
            'foreign_keys' => $this->getForeignKeys($config),
        ]);

        $this->createFile($migrationPath, $migrationContent);
    }

    protected function buildIndexes(array $config): void
    {
        $indexes = $config['indexes'] ?? [];
        if (empty($indexes)) {
            return;
        }

        $tableName = $this->getTableName($config['name']);
        $migrationName = $this->getMigrationName($tableName, 'indexes');
        $migrationPath = database_path("migrations/{$migrationName}.php");
        $migrationStub = $this->getStub('indexes');
        $migrationContent = $this->replaceStub($migrationStub, [
            'table' => $tableName,
            'indexes' => $this->getIndexes($config),
        ]);

        $this->createFile($migrationPath, $migrationContent);
    }

    protected function getMigrationColumns(array $config): string
    {
        $columns = $config['columns'] ?? [
            '$table->id();',
            '$table->timestamps();',
        ];

        return implode("\n            ", $columns);
    }

    protected function getForeignKeys(array $config): string
    {
        $foreignKeys = $config['foreign_keys'] ?? [];
        $result = [];

        foreach ($foreignKeys as $key) {
            $result[] = "\$table->foreign('{$key['column']}')" .
                "->references('{$key['references']}')" .
                "->on('{$key['on']}')" .
                ($key['on_delete'] ? "->onDelete('{$key['on_delete']}')" : '') .
                ($key['on_update'] ? "->onUpdate('{$key['on_update']}')" : '') .
                ';';
        }

        return implode("\n            ", $result);
    }

    protected function getIndexes(array $config): string
    {
        $indexes = $config['indexes'] ?? [];
        $result = [];

        foreach ($indexes as $index) {
            $columns = is_array($index['columns']) ? $index['columns'] : [$index['columns']];
            $result[] = "\$table->index(['" . implode("', '", $columns) . "'], '{$index['name']}');";
        }

        return implode("\n            ", $result);
    }

    protected function getTableName(string $model): string
    {
        return Str::plural(Str::snake($model));
    }

    protected function getMigrationName(string $table, string $type = 'create'): string
    {
        return date('Y_m_d_His') . "_{$type}_{$table}_table";
    }
} 