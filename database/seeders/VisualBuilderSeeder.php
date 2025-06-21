<?php

namespace LaravelBuilder\VisualBuilder\Database\Seeders;

use Illuminate\Database\Seeder;
use LaravelBuilder\VisualBuilder\Models\Category;
use LaravelBuilder\VisualBuilder\Models\Component;
use LaravelBuilder\VisualBuilder\Models\Layout;

class VisualBuilderSeeder extends Seeder
{
    public function run(): void
    {
        // Create default categories
        $categories = [
            [
                'name' => 'Basic',
                'description' => 'Basic components for building simple interfaces',
                'icon' => 'square',
                'order' => 1,
            ],
            [
                'name' => 'Layout',
                'description' => 'Layout components for structuring your pages',
                'icon' => 'grid',
                'order' => 2,
            ],
            [
                'name' => 'Navigation',
                'description' => 'Navigation components for site structure',
                'icon' => 'menu',
                'order' => 3,
            ],
            [
                'name' => 'Data',
                'description' => 'Data display components',
                'icon' => 'table',
                'order' => 4,
            ],
            [
                'name' => 'Form',
                'description' => 'Form components for user input',
                'icon' => 'input',
                'order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create default layouts
        $layouts = [
            [
                'name' => 'app',
                'description' => 'Default application layout',
                'template' => '<!DOCTYPE html>
<html>
<head>
    <title>{{ $title ?? config("app.name") }}</title>
    {{ region:head }}
</head>
<body>
    <header>
        {{ region:header }}
    </header>
    <main>
        {{ region:content }}
    </main>
    <footer>
        {{ region:footer }}
    </footer>
</body>
</html>',
                'regions' => [
                    ['name' => 'head', 'description' => 'Head section'],
                    ['name' => 'header', 'description' => 'Header section'],
                    ['name' => 'content', 'description' => 'Main content'],
                    ['name' => 'footer', 'description' => 'Footer section'],
                ],
                'is_system' => true,
            ],
            [
                'name' => 'auth',
                'description' => 'Authentication layout',
                'template' => '<!DOCTYPE html>
<html>
<head>
    <title>{{ $title ?? "Authentication" }}</title>
    {{ region:head }}
</head>
<body>
    <div class="auth-container">
        {{ region:content }}
    </div>
</body>
</html>',
                'regions' => [
                    ['name' => 'head', 'description' => 'Head section'],
                    ['name' => 'content', 'description' => 'Main content'],
                ],
                'is_system' => true,
            ],
        ];

        foreach ($layouts as $layout) {
            Layout::create($layout);
        }

        // Create default components
        $basicCategory = Category::where('name', 'Basic')->first();
        $components = [
            [
                'name' => 'Text',
                'type' => 'text',
                'category_id' => $basicCategory->id,
                'properties' => [
                    'content' => ['type' => 'string', 'default' => 'Text content'],
                    'size' => ['type' => 'string', 'default' => 'base'],
                    'color' => ['type' => 'string', 'default' => 'inherit'],
                ],
                'template' => '<div class="text-{{ $size }} text-{{ $color }}">{{ $content }}</div>',
                'is_active' => true,
                'is_custom' => false,
            ],
            [
                'name' => 'Button',
                'type' => 'button',
                'category_id' => $basicCategory->id,
                'properties' => [
                    'text' => ['type' => 'string', 'default' => 'Button'],
                    'type' => ['type' => 'string', 'default' => 'button'],
                    'variant' => ['type' => 'string', 'default' => 'primary'],
                ],
                'template' => '<button type="{{ $type }}" class="btn btn-{{ $variant }}">{{ $text }}</button>',
                'is_active' => true,
                'is_custom' => false,
            ],
        ];

        foreach ($components as $component) {
            Component::create($component);
        }
    }
} 