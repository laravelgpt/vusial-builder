<?php

namespace LaravelBuilder\VisualBuilder;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use LaravelBuilder\VisualBuilder\Services\ApiBuilder;
use LaravelBuilder\VisualBuilder\Services\AuthBuilder;
use LaravelBuilder\VisualBuilder\Services\ComponentBuilder;
use LaravelBuilder\VisualBuilder\Services\AIService;
use LaravelBuilder\VisualBuilder\Services\ThemeBuilder;
use LaravelBuilder\VisualBuilder\Services\MenuBuilder;
use LaravelBuilder\VisualBuilder\Services\RouteBuilder;
use LaravelBuilder\VisualBuilder\Services\ModelBuilder;
use LaravelBuilder\VisualBuilder\Services\ControllerBuilder;
use LaravelBuilder\VisualBuilder\Services\MigrationBuilder;
use LaravelBuilder\VisualBuilder\Services\SeederBuilder;
use LaravelBuilder\VisualBuilder\Services\PolicyBuilder;
use LaravelBuilder\VisualBuilder\Services\FormBuilder;
use LaravelBuilder\VisualBuilder\Services\ModuleBuilder;
use LaravelBuilder\VisualBuilder\Services\CommandBuilder;
use LaravelBuilder\VisualBuilder\Services\ServiceProviderBuilder;
use LaravelBuilder\VisualBuilder\Services\ConfigBuilder;
use LaravelBuilder\VisualBuilder\Services\WidgetBuilder;

class VisualBuilder
{
    protected $app;
    protected $apiBuilder;
    protected $authBuilder;
    protected $componentBuilder;
    protected $aiService;
    protected $themeBuilder;
    protected $menuBuilder;
    protected $routeBuilder;
    protected $modelBuilder;
    protected $controllerBuilder;
    protected $migrationBuilder;
    protected $seederBuilder;
    protected $policyBuilder;
    protected $formBuilder;
    protected $moduleBuilder;
    protected $commandBuilder;
    protected $serviceProviderBuilder;
    protected $configBuilder;
    protected $widgetBuilder;

    public function __construct($app)
    {
        $this->app = $app;
        $this->initializeServices();
    }

    protected function initializeServices()
    {
        $this->apiBuilder = new ApiBuilder();
        $this->authBuilder = new AuthBuilder();
        $this->componentBuilder = new ComponentBuilder();
        $this->aiService = new AIService();
        $this->themeBuilder = new ThemeBuilder();
        $this->menuBuilder = new MenuBuilder();
        $this->routeBuilder = new RouteBuilder();
        $this->modelBuilder = new ModelBuilder();
        $this->controllerBuilder = new ControllerBuilder();
        $this->migrationBuilder = new MigrationBuilder();
        $this->seederBuilder = new SeederBuilder();
        $this->policyBuilder = new PolicyBuilder();
        $this->formBuilder = new FormBuilder();
        $this->moduleBuilder = new ModuleBuilder();
        $this->commandBuilder = new CommandBuilder();
        $this->serviceProviderBuilder = new ServiceProviderBuilder();
        $this->configBuilder = new ConfigBuilder();
        $this->widgetBuilder = new WidgetBuilder();
    }

    public function buildPage(array $config)
    {
        return $this->componentBuilder->buildPage($config);
    }

    public function buildMenu(array $config)
    {
        return $this->menuBuilder->build($config);
    }

    public function buildApi(array $config)
    {
        return $this->apiBuilder->build($config);
    }

    public function buildAuth(array $config)
    {
        return $this->authBuilder->build($config);
    }

    public function buildTheme(array $config)
    {
        return $this->themeBuilder->build($config);
    }

    public function buildRoute(array $config)
    {
        return $this->routeBuilder->build($config);
    }

    public function buildModel(array $config)
    {
        return $this->modelBuilder->build($config);
    }

    public function buildController(array $config)
    {
        return $this->controllerBuilder->build($config);
    }

    public function buildMigration(array $config)
    {
        return $this->migrationBuilder->build($config);
    }

    public function buildSeeder(array $config)
    {
        return $this->seederBuilder->build($config);
    }

    public function buildPolicy(array $config)
    {
        return $this->policyBuilder->build($config);
    }

    public function buildForm(array $config)
    {
        return $this->formBuilder->build($config);
    }

    public function buildModule(array $config)
    {
        return $this->moduleBuilder->build($config);
    }

    public function buildCommand(array $config)
    {
        return $this->commandBuilder->build($config);
    }

    public function buildServiceProvider(array $config)
    {
        return $this->serviceProviderBuilder->build($config);
    }

    public function buildConfig(array $config)
    {
        return $this->configBuilder->build($config);
    }

    public function buildWidget(array $config)
    {
        return $this->widgetBuilder->build($config);
    }

    public function generateAISuggestions(string $prompt)
    {
        return $this->aiService->generateSuggestions($prompt);
    }

    public function exportPostmanCollection()
    {
        return $this->apiBuilder->exportPostmanCollection();
    }

    public function generateOpenApiDocs()
    {
        return $this->apiBuilder->generateOpenApiDocs();
    }

    public function getBuilderInterface()
    {
        return View::make('visual-builder::interface');
    }

    public function getAvailableBuilders()
    {
        return [
            'page' => [
                'name' => 'Page Builder',
                'description' => 'Visual page designer',
                'features' => [
                    'Layout templates',
                    'Component library',
                    'Responsive design',
                    'SEO optimization',
                ],
            ],
            'menu' => [
                'name' => 'Menu Builder',
                'description' => 'Navigation menu designer',
                'features' => [
                    'Menu types',
                    'Dropdown support',
                    'Mega menu',
                    'Mobile menu',
                ],
            ],
            'route' => [
                'name' => 'Route Builder',
                'description' => 'Route configuration tool',
                'features' => [
                    'Route groups',
                    'Middleware',
                    'Parameters',
                    'Constraints',
                ],
            ],
            'model' => [
                'name' => 'Model Builder',
                'description' => 'Database model generator',
                'features' => [
                    'Relationships',
                    'Attributes',
                    'Scopes',
                    'Events',
                ],
            ],
            'controller' => [
                'name' => 'Controller Builder',
                'description' => 'Controller generator',
                'features' => [
                    'CRUD operations',
                    'Resource methods',
                    'Validation',
                    'Authorization',
                ],
            ],
            'api' => [
                'name' => 'API Builder',
                'description' => 'API endpoint generator',
                'features' => [
                    'REST endpoints',
                    'GraphQL schema',
                    'Documentation',
                    'Testing',
                ],
            ],
            'auth' => [
                'name' => 'Auth Builder',
                'description' => 'Authentication system generator',
                'features' => [
                    'Social login',
                    'Role management',
                    'Permissions',
                    'Policies',
                ],
            ],
            'component' => [
                'name' => 'Component Builder',
                'description' => 'UI component generator',
                'features' => [
                    'Livewire components',
                    'Vue components',
                    'Blade components',
                    'Props',
                ],
            ],
            'theme' => [
                'name' => 'Theme Builder',
                'description' => 'Theme and style generator',
                'features' => [
                    'Color schemes',
                    'Typography',
                    'Dark mode',
                    'Custom CSS',
                ],
            ],
        ];
    }

    public function getBuilderConfig(string $builder)
    {
        return Config::get("visual-builder.builders.{$builder}");
    }

    public function getBuilderFeatures(string $builder)
    {
        $config = $this->getBuilderConfig($builder);
        return $config['features'] ?? [];
    }

    public function getBuilderDependencies(string $builder)
    {
        $config = $this->getBuilderConfig($builder);
        return $config['dependencies'] ?? [];
    }

    public function getBuilderRequirements(string $builder)
    {
        $config = $this->getBuilderConfig($builder);
        return $config['requirements'] ?? [];
    }

    public function validateBuilderConfig(string $builder, array $config)
    {
        $requirements = $this->getBuilderRequirements($builder);
        $errors = [];

        foreach ($requirements as $field => $rules) {
            if (!isset($config[$field])) {
                $errors[$field] = "The {$field} field is required.";
                continue;
            }

            if (is_array($rules)) {
                foreach ($rules as $rule) {
                    if (!$this->validateRule($rule, $config[$field])) {
                        $errors[$field] = "The {$field} field is invalid.";
                        break;
                    }
                }
            }
        }

        return $errors;
    }

    protected function validateRule($rule, $value)
    {
        if (is_string($rule)) {
            return $this->validateStringRule($rule, $value);
        }

        if (is_array($rule)) {
            return $this->validateArrayRule($rule, $value);
        }

        return true;
    }

    protected function validateStringRule($rule, $value)
    {
        switch ($rule) {
            case 'required':
                return !empty($value);
            case 'string':
                return is_string($value);
            case 'array':
                return is_array($value);
            case 'numeric':
                return is_numeric($value);
            case 'boolean':
                return is_bool($value);
            default:
                return true;
        }
    }

    protected function validateArrayRule($rule, $value)
    {
        if (!is_array($value)) {
            return false;
        }

        foreach ($rule as $key => $subRule) {
            if (!isset($value[$key])) {
                return false;
            }

            if (!$this->validateRule($subRule, $value[$key])) {
                return false;
            }
        }

        return true;
    }
} 