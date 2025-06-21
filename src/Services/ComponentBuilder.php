<?php

namespace LaravelBuilder\VisualBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LaravelBuilder\VisualBuilder\Generators\PageGenerator;
use LaravelBuilder\VisualBuilder\Generators\MenuGenerator;
use LaravelBuilder\VisualBuilder\Generators\ComponentGenerator;
use LaravelBuilder\VisualBuilder\Generators\LayoutGenerator;

class ComponentBuilder
{
    protected $pageGenerator;
    protected $menuGenerator;
    protected $componentGenerator;
    protected $layoutGenerator;

    public function __construct()
    {
        $this->pageGenerator = new PageGenerator();
        $this->menuGenerator = new MenuGenerator();
        $this->componentGenerator = new ComponentGenerator();
        $this->layoutGenerator = new LayoutGenerator();
    }

    public function buildPage(array $config)
    {
        $this->validatePageConfig($config);

        // Generate page components
        $this->pageGenerator->generate($config);
        $this->generatePageComponents($config);

        return [
            'status' => 'success',
            'message' => 'Page built successfully',
            'components' => $this->getGeneratedPageComponents($config),
        ];
    }

    public function buildMenu(array $config)
    {
        $this->validateMenuConfig($config);

        // Generate menu components
        $this->menuGenerator->generate($config);

        return [
            'status' => 'success',
            'message' => 'Menu built successfully',
            'components' => $this->getGeneratedMenuComponents($config),
        ];
    }

    protected function validatePageConfig(array $config)
    {
        if (!isset($config['name']) || !isset($config['layout'])) {
            throw new \InvalidArgumentException('Page name and layout are required');
        }
    }

    protected function validateMenuConfig(array $config)
    {
        if (!isset($config['name']) || !isset($config['items'])) {
            throw new \InvalidArgumentException('Menu name and items are required');
        }
    }

    protected function generatePageComponents(array $config)
    {
        if (isset($config['components'])) {
            foreach ($config['components'] as $component) {
                $this->componentGenerator->generate($component);
            }
        }
    }

    public function generateLayout(array $config)
    {
        $this->validateLayoutConfig($config);

        // Generate layout components
        $this->layoutGenerator->generate($config);

        return [
            'status' => 'success',
            'message' => 'Layout built successfully',
            'components' => $this->getGeneratedLayoutComponents($config),
        ];
    }

    protected function validateLayoutConfig(array $config)
    {
        if (!isset($config['name']) || !isset($config['type'])) {
            throw new \InvalidArgumentException('Layout name and type are required');
        }
    }

    public function getAvailableComponents()
    {
        return [
            'basic' => [
                'text',
                'image',
                'button',
                'input',
                'textarea',
                'select',
                'checkbox',
                'radio',
            ],
            'layout' => [
                'container',
                'row',
                'column',
                'card',
                'modal',
                'tabs',
                'accordion',
            ],
            'navigation' => [
                'navbar',
                'sidebar',
                'breadcrumb',
                'pagination',
                'menu',
            ],
            'data' => [
                'table',
                'list',
                'grid',
                'chart',
                'calendar',
            ],
            'form' => [
                'form',
                'fieldset',
                'file-upload',
                'date-picker',
                'time-picker',
                'color-picker',
            ],
        ];
    }

    public function getAvailableLayouts()
    {
        return [
            'app' => 'Application Layout',
            'auth' => 'Authentication Layout',
            'admin' => 'Admin Dashboard Layout',
            'landing' => 'Landing Page Layout',
            'blog' => 'Blog Layout',
            'shop' => 'E-commerce Layout',
        ];
    }

    protected function getGeneratedPageComponents(array $config)
    {
        return [
            'page' => $this->pageGenerator->getGeneratedFiles(),
            'components' => $this->componentGenerator->getGeneratedFiles(),
        ];
    }

    protected function getGeneratedMenuComponents(array $config)
    {
        return [
            'menu' => $this->menuGenerator->getGeneratedFiles(),
        ];
    }

    protected function getGeneratedLayoutComponents(array $config)
    {
        return [
            'layout' => $this->layoutGenerator->getGeneratedFiles(),
        ];
    }

    public function exportComponentConfig(array $config)
    {
        return [
            'name' => $config['name'],
            'type' => $config['type'],
            'properties' => $config['properties'] ?? [],
            'children' => $config['children'] ?? [],
            'styles' => $config['styles'] ?? [],
            'scripts' => $config['scripts'] ?? [],
        ];
    }

    public function importComponentConfig(array $config)
    {
        $this->validateComponentConfig($config);
        return $this->componentGenerator->generate($config);
    }

    protected function validateComponentConfig(array $config)
    {
        if (!isset($config['name']) || !isset($config['type'])) {
            throw new \InvalidArgumentException('Component name and type are required');
        }
    }
} 