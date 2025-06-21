<?php

namespace App\Builders;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class PageBuilder extends BaseBuilder
{
    protected function getDefaultName(): string
    {
        return 'page';
    }

    protected function getConfigPath(): string
    {
        return __DIR__ . '/../../config/page-builder.php';
    }

    protected function getStubsPath(): string
    {
        return __DIR__ . '/../../stubs/page';
    }

    public function build(): bool
    {
        $this->output('Building page components...');

        // Create page model
        $this->createPageModel();

        // Create page controller
        $this->createPageController();

        // Create page views
        $this->createPageViews();

        // Create page routes
        $this->createPageRoutes();

        // Create page migrations
        $this->createPageMigrations();

        // Create page seeders
        $this->createPageSeeders();

        return true;
    }

    protected function createPageModel(): bool
    {
        $stub = $this->getStub('model');
        if (!$stub) {
            return false;
        }

        $replacements = [
            '{{namespace}}' => $this->getNamespace('Models'),
            '{{class}}' => $this->getClassName('Page'),
            '{{table}}' => $this->getTableName('page'),
        ];

        $content = $this->replaceStub($stub, $replacements);
        $path = app_path('Models/Page.php');

        return $this->createFile($path, $content);
    }

    protected function createPageController(): bool
    {
        $stub = $this->getStub('controller');
        if (!$stub) {
            return false;
        }

        $replacements = [
            '{{namespace}}' => $this->getNamespace('Http/Controllers'),
            '{{class}}' => $this->getClassName('PageController'),
            '{{model}}' => $this->getClassName('Page'),
            '{{view}}' => $this->getViewName('page'),
        ];

        $content = $this->replaceStub($stub, $replacements);
        $path = app_path('Http/Controllers/PageController.php');

        return $this->createFile($path, $content);
    }

    protected function createPageViews(): bool
    {
        $views = [
            'index' => 'pages/index.blade.php',
            'show' => 'pages/show.blade.php',
            'create' => 'pages/create.blade.php',
            'edit' => 'pages/edit.blade.php',
        ];

        foreach ($views as $view => $path) {
            $stub = $this->getStub("views/{$view}");
            if (!$stub) {
                continue;
            }

            $replacements = [
                '{{title}}' => Str::title($view),
                '{{model}}' => $this->getClassName('Page'),
                '{{route}}' => $this->getRouteName('page'),
            ];

            $content = $this->replaceStub($stub, $replacements);
            $fullPath = resource_path("views/{$path}");

            $this->createFile($fullPath, $content);
        }

        return true;
    }

    protected function createPageRoutes(): bool
    {
        $stub = $this->getStub('routes');
        if (!$stub) {
            return false;
        }

        $replacements = [
            '{{controller}}' => $this->getClassName('PageController'),
            '{{route}}' => $this->getRouteName('page'),
        ];

        $content = $this->replaceStub($stub, $replacements);
        $path = base_path('routes/web.php');

        // Append routes to existing file
        if (File::exists($path)) {
            $content = File::get($path) . "\n" . $content;
        }

        return $this->createFile($path, $content);
    }

    protected function createPageMigrations(): bool
    {
        $stub = $this->getStub('migration');
        if (!$stub) {
            return false;
        }

        $replacements = [
            '{{table}}' => $this->getTableName('page'),
        ];

        $content = $this->replaceStub($stub, $replacements);
        $path = database_path("migrations/{$this->getMigrationName('page')}.php");

        return $this->createFile($path, $content);
    }

    protected function createPageSeeders(): bool
    {
        $stub = $this->getStub('seeder');
        if (!$stub) {
            return false;
        }

        $replacements = [
            '{{namespace}}' => $this->getNamespace('Database/Seeders'),
            '{{class}}' => $this->getSeederName('Page'),
            '{{model}}' => $this->getClassName('Page'),
        ];

        $content = $this->replaceStub($stub, $replacements);
        $path = database_path("seeders/{$this->getSeederName('Page')}.php");

        return $this->createFile($path, $content);
    }
} 