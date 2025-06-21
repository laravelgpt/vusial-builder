<?php

namespace LaravelBuilder\VisualBuilder\Services;

use LaravelBuilder\VisualBuilder\Models\Api;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ApiGeneratorService
{
    protected $stubsPath;
    protected $outputPath;

    public function __construct()
    {
        $this->stubsPath = __DIR__ . '/../stubs/api';
        $this->outputPath = app_path('Http/Controllers/Api');
    }

    public function generate(Api $api): void
    {
        $method = 'generate' . Str::studly($api->type) . 'Api';
        if (method_exists($this, $method)) {
            $this->$method($api);
        } else {
            throw new \Exception("Unsupported API type: {$api->type}");
        }

        // Generate builder-specific components
        $builderMethod = 'generate' . Str::studly($api->builder_type) . 'Builder';
        if (method_exists($this, $builderMethod)) {
            $this->$builderMethod($api);
        }

        // Generate authentication if needed
        if ($api->auth_type !== 'none') {
            $this->generateAuthentication($api);
        }

        // Generate rate limiting if configured
        if ($api->rate_limit) {
            $this->generateRateLimiting($api);
        }

        // Generate middleware if configured
        if ($api->middleware) {
            $this->generateMiddleware($api);
        }

        // Generate AI suggestions if available
        if ($api->ai_suggestions) {
            $this->generateAiSuggestions($api);
        }
    }

    public function regenerate(Api $api): void
    {
        // Delete existing files
        $this->deleteExistingFiles($api);
        // Generate new files
        $this->generate($api);
    }

    protected function generateRestApi(Api $api): void
    {
        $controllerName = Str::studly($api->name) . 'Controller';
        $modelName = Str::studly($api->model);
        
        // Generate Controller
        $controllerContent = $this->getStub('rest/controller.stub');
        $controllerContent = $this->replacePlaceholders($controllerContent, [
            'DummyController' => $controllerName,
            'DummyModel' => $modelName,
            'dummyModel' => Str::camel($modelName),
            'dummyModelPlural' => Str::plural(Str::camel($modelName)),
        ]);
        
        $this->createFile("{$controllerName}.php", $controllerContent);

        // Generate Routes
        $routesContent = $this->getStub('rest/routes.stub');
        $routesContent = $this->replacePlaceholders($routesContent, [
            'DummyController' => $controllerName,
            'dummyModelPlural' => Str::plural(Str::kebab($modelName)),
        ]);
        
        $this->appendToRoutesFile($routesContent);

        // Generate API Resource
        $resourceContent = $this->getStub('rest/resource.stub');
        $resourceContent = $this->replacePlaceholders($resourceContent, [
            'DummyResource' => "{$modelName}Resource",
            'DummyModel' => $modelName,
        ]);
        
        $this->createFile("Resources/{$modelName}Resource.php", $resourceContent);
    }

    protected function generateJsonApi(Api $api): void
    {
        $controllerName = Str::studly($api->name) . 'Controller';
        $modelName = Str::studly($api->model);
        
        // Generate Controller
        $controllerContent = $this->getStub('json_api/controller.stub');
        $controllerContent = $this->replacePlaceholders($controllerContent, [
            'DummyController' => $controllerName,
            'DummyModel' => $modelName,
            'dummyModel' => Str::camel($modelName),
            'dummyModelPlural' => Str::plural(Str::camel($modelName)),
        ]);
        
        $this->createFile("{$controllerName}.php", $controllerContent);

        // Generate Resource
        $resourceContent = $this->getStub('json_api/resource.stub');
        $resourceContent = $this->replacePlaceholders($resourceContent, [
            'DummyResource' => "{$modelName}Resource",
            'DummyModel' => $modelName,
        ]);
        
        $this->createFile("Resources/{$modelName}Resource.php", $resourceContent);
    }

    protected function generateHypermediaApi(Api $api): void
    {
        $controllerName = Str::studly($api->name) . 'Controller';
        $modelName = Str::studly($api->model);
        
        // Generate Controller
        $controllerContent = $this->getStub('hypermedia/controller.stub');
        $controllerContent = $this->replacePlaceholders($controllerContent, [
            'DummyController' => $controllerName,
            'DummyModel' => $modelName,
            'dummyModel' => Str::camel($modelName),
            'dummyModelPlural' => Str::plural(Str::camel($modelName)),
        ]);
        
        $this->createFile("{$controllerName}.php", $controllerContent);

        // Generate Resource
        $resourceContent = $this->getStub('hypermedia/resource.stub');
        $resourceContent = $this->replacePlaceholders($resourceContent, [
            'DummyResource' => "{$modelName}Resource",
            'DummyModel' => $modelName,
        ]);
        
        $this->createFile("Resources/{$modelName}Resource.php", $resourceContent);
    }

    protected function generateCurlApi(Api $api): void
    {
        $serviceName = Str::studly($api->name) . 'Service';
        
        // Generate Service
        $serviceContent = $this->getStub('curl/service.stub');
        $serviceContent = $this->replacePlaceholders($serviceContent, [
            'DummyService' => $serviceName,
            'dummyEndpoint' => Str::kebab($api->name),
        ]);
        
        $this->createFile("Services/{$serviceName}.php", $serviceContent);

        // Generate Example Script
        $scriptContent = $this->getStub('curl/example.stub');
        $scriptContent = $this->replacePlaceholders($scriptContent, [
            'dummyEndpoint' => Str::kebab($api->name),
        ]);
        
        $this->createFile("Examples/{$api->name}.sh", $scriptContent);
    }

    protected function generateGraphqlApi(Api $api): void
    {
        $typeName = Str::studly($api->name);
        
        // Generate Type
        $typeContent = $this->getStub('graphql/type.stub');
        $typeContent = $this->replacePlaceholders($typeContent, [
            'DummyType' => $typeName,
        ]);
        
        $this->createFile("GraphQL/Types/{$typeName}Type.php", $typeContent);

        // Generate Query
        $queryContent = $this->getStub('graphql/query.stub');
        $queryContent = $this->replacePlaceholders($queryContent, [
            'DummyQuery' => "{$typeName}Query",
            'DummyType' => $typeName,
        ]);
        
        $this->createFile("GraphQL/Queries/{$typeName}Query.php", $queryContent);

        // Generate Mutation
        $mutationContent = $this->getStub('graphql/mutation.stub');
        $mutationContent = $this->replacePlaceholders($mutationContent, [
            'DummyMutation' => "{$typeName}Mutation",
            'DummyType' => $typeName,
        ]);
        
        $this->createFile("GraphQL/Mutations/{$typeName}Mutation.php", $mutationContent);
    }

    protected function generateSoapApi(Api $api): void
    {
        $serviceName = Str::studly($api->name) . 'Service';
        
        // Generate Service
        $serviceContent = $this->getStub('soap/service.stub');
        $serviceContent = $this->replacePlaceholders($serviceContent, [
            'DummyService' => $serviceName,
        ]);
        
        $this->createFile("Services/{$serviceName}.php", $serviceContent);

        // Generate WSDL
        $wsdlContent = $this->getStub('soap/wsdl.stub');
        $wsdlContent = $this->replacePlaceholders($wsdlContent, [
            'dummyService' => Str::kebab($api->name),
        ]);
        
        $this->createFile("WSDL/{$api->name}.wsdl", $wsdlContent);
    }

    protected function generateGrpcApi(Api $api): void
    {
        $serviceName = Str::studly($api->name) . 'Service';
        
        // Generate Service
        $serviceContent = $this->getStub('grpc/service.stub');
        $serviceContent = $this->replacePlaceholders($serviceContent, [
            'DummyService' => $serviceName,
        ]);
        
        $this->createFile("Services/{$serviceName}.php", $serviceContent);

        // Generate Proto
        $protoContent = $this->getStub('grpc/proto.stub');
        $protoContent = $this->replacePlaceholders($protoContent, [
            'dummyService' => Str::kebab($api->name),
        ]);
        
        $this->createFile("Proto/{$api->name}.proto", $protoContent);
    }

    protected function generateAuthentication(Api $api): void
    {
        $authType = $api->auth_type;
        $method = 'generate' . Str::studly($authType) . 'Auth';
        
        if (method_exists($this, $method)) {
            $this->$method($api);
        }
    }

    protected function generateTokenAuth(Api $api): void
    {
        // Generate Sanctum/Passport configuration
        $configContent = $this->getStub('auth/token.stub');
        $this->createFile("Auth/TokenConfig.php", $configContent);
    }

    protected function generateOAuthAuth(Api $api): void
    {
        // Generate OAuth configuration
        $configContent = $this->getStub('auth/oauth.stub');
        $this->createFile("Auth/OAuthConfig.php", $configContent);
    }

    protected function generateJwtAuth(Api $api): void
    {
        // Generate JWT configuration
        $configContent = $this->getStub('auth/jwt.stub');
        $this->createFile("Auth/JwtConfig.php", $configContent);
    }

    protected function generateSocialAuth(Api $api): void
    {
        // Generate social authentication configuration
        $configContent = $this->getStub('auth/social.stub');
        $this->createFile("Auth/SocialConfig.php", $configContent);

        // Generate social login controller
        $controllerContent = $this->getStub('auth/social_controller.stub');
        $this->createFile("Controllers/Auth/SocialLoginController.php", $controllerContent);

        // Generate social callback controller
        $callbackContent = $this->getStub('auth/social_callback.stub');
        $this->createFile("Controllers/Auth/SocialCallbackController.php", $callbackContent);

        // Generate social service provider
        $providerContent = $this->getStub('auth/social_provider.stub');
        $this->createFile("Providers/SocialAuthServiceProvider.php", $providerContent);

        // Generate social routes
        $routesContent = $this->getStub('auth/social_routes.stub');
        $this->appendToRoutesFile($routesContent);

        // Generate social login views
        $this->generateSocialLoginViews($api);
    }

    protected function generateSocialLoginViews(Api $api): void
    {
        // Generate social login button component
        $buttonContent = $this->getStub('auth/views/social_button.stub');
        $this->createFile("resources/views/components/social-login-button.blade.php", $buttonContent);

        // Generate social login form
        $formContent = $this->getStub('auth/views/social_form.stub');
        $this->createFile("resources/views/auth/social-login.blade.php", $formContent);

        // Generate social callback view
        $callbackContent = $this->getStub('auth/views/social_callback.stub');
        $this->createFile("resources/views/auth/social-callback.blade.php", $callbackContent);
    }

    protected function generateRateLimiting(Api $api): void
    {
        $configContent = $this->getStub('rate_limiting/config.stub');
        $this->createFile("RateLimiting/{$api->name}Config.php", $configContent);
    }

    protected function generateMiddleware(Api $api): void
    {
        $middlewareContent = $this->getStub('middleware/api.stub');
        $this->createFile("Middleware/{$api->name}Middleware.php", $middlewareContent);
    }

    protected function generateAiSuggestions(Api $api): void
    {
        $suggestionsContent = $this->getStub('ai/suggestions.stub');
        $this->createFile("AI/{$api->name}Suggestions.php", $suggestionsContent);
    }

    public function export(Api $api, string $format = 'postman'): \Symfony\Component\HttpFoundation\Response
    {
        $method = 'exportTo' . Str::studly($format);
        if (method_exists($this, $method)) {
            return $this->$method($api);
        }
        throw new \Exception("Unsupported export format: {$format}");
    }

    protected function exportToPostman(Api $api): \Symfony\Component\HttpFoundation\Response
    {
        $collection = [
            'info' => [
                'name' => $api->name,
                'description' => "API for {$api->model}",
                'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
            ],
            'item' => [],
        ];

        foreach ($api->endpoints as $endpoint) {
            $collection['item'][] = [
                'name' => $endpoint['name'],
                'request' => [
                    'method' => $endpoint['method'],
                    'url' => [
                        'raw' => $endpoint['path'],
                        'path' => explode('/', trim($endpoint['path'], '/')),
                    ],
                ],
            ];
        }

        return response()->json($collection)
            ->header('Content-Disposition', "attachment; filename=\"{$api->name}-postman.json\"")
            ->header('Content-Type', 'application/json');
    }

    protected function exportToOpenApi(Api $api): \Symfony\Component\HttpFoundation\Response
    {
        $spec = [
            'openapi' => '3.0.0',
            'info' => [
                'title' => $api->name,
                'description' => "API for {$api->model}",
                'version' => '1.0.0',
            ],
            'paths' => [],
        ];

        foreach ($api->endpoints as $endpoint) {
            $spec['paths'][$endpoint['path']] = [
                $endpoint['method'] => [
                    'summary' => $endpoint['name'],
                    'responses' => [
                        '200' => [
                            'description' => 'Successful operation',
                        ],
                    ],
                ],
            ];
        }

        return response()->json($spec)
            ->header('Content-Disposition', "attachment; filename=\"{$api->name}-openapi.json\"")
            ->header('Content-Type', 'application/json');
    }

    protected function getStub(string $stub): string
    {
        return File::get("{$this->stubsPath}/{$stub}");
    }

    protected function replacePlaceholders(string $content, array $replacements): string
    {
        foreach ($replacements as $key => $value) {
            $content = str_replace("{{ {$key} }}", $value, $content);
        }
        return $content;
    }

    protected function createFile(string $path, string $content): void
    {
        $fullPath = "{$this->outputPath}/{$path}";
        File::makeDirectory(dirname($fullPath), 0755, true, true);
        File::put($fullPath, $content);
    }

    protected function appendToRoutesFile(string $content): void
    {
        $routesPath = base_path('routes/api.php');
        File::append($routesPath, "\n{$content}");
    }

    protected function deleteExistingFiles(Api $api): void
    {
        $basePath = "{$this->outputPath}/" . Str::studly($api->name);
        if (File::exists($basePath)) {
            File::deleteDirectory($basePath);
        }
    }

    protected function generateFormBuilder(Api $api): void
    {
        // Generate form configuration
        $configContent = $this->getStub('form/config.stub');
        $this->createFile("Form/{$api->name}Config.php", $configContent);

        // Generate form controller
        $controllerContent = $this->getStub('form/controller.stub');
        $this->createFile("Controllers/Form/{$api->name}Controller.php", $controllerContent);

        // Generate form views
        $this->generateFormViews($api);

        // Generate form validation
        $validationContent = $this->getStub('form/validation.stub');
        $this->createFile("Requests/Form/{$api->name}Request.php", $validationContent);

        // Generate form actions
        $actionsContent = $this->getStub('form/actions.stub');
        $this->createFile("Actions/Form/{$api->name}Actions.php", $actionsContent);
    }

    protected function generateFormViews(Api $api): void
    {
        // Generate form layout
        $layoutContent = $this->getStub('form/views/layout.stub');
        $this->createFile("resources/views/forms/{$api->name}/layout.blade.php", $layoutContent);

        // Generate form fields
        foreach ($api->fields as $field) {
            $fieldContent = $this->getStub("form/views/fields/{$field['type']}.stub");
            $this->createFile("resources/views/forms/{$api->name}/fields/{$field['name']}.blade.php", $fieldContent);
        }

        // Generate form actions
        $actionsContent = $this->getStub('form/views/actions.stub');
        $this->createFile("resources/views/forms/{$api->name}/actions.blade.php", $actionsContent);
    }

    protected function generateTableBuilder(Api $api): void
    {
        // Generate table configuration
        $configContent = $this->getStub('table/config.stub');
        $this->createFile("Table/{$api->name}Config.php", $configContent);

        // Generate table controller
        $controllerContent = $this->getStub('table/controller.stub');
        $this->createFile("Controllers/Table/{$api->name}Controller.php", $controllerContent);

        // Generate table views
        $this->generateTableView($api);

        // Generate table actions
        $actionsContent = $this->getStub('table/actions.stub');
        $this->createFile("Actions/Table/{$api->name}Actions.php", $actionsContent);

        // Generate table exports
        $exportContent = $this->getStub('table/export.stub');
        $this->createFile("Exports/Table/{$api->name}Export.php", $exportContent);
    }

    protected function generateTableView(Api $api): void
    {
        // Generate table layout
        $layoutContent = $this->getStub('table/views/layout.stub');
        $this->createFile("resources/views/tables/{$api->name}/layout.blade.php", $layoutContent);

        // Generate table columns
        foreach ($api->fields as $field) {
            $columnContent = $this->getStub("table/views/columns/{$field['type']}.stub");
            $this->createFile("resources/views/tables/{$api->name}/columns/{$field['name']}.blade.php", $columnContent);
        }

        // Generate table filters
        $filtersContent = $this->getStub('table/views/filters.stub');
        $this->createFile("resources/views/tables/{$api->name}/filters.blade.php", $filtersContent);

        // Generate table actions
        $actionsContent = $this->getStub('table/views/actions.stub');
        $this->createFile("resources/views/tables/{$api->name}/actions.blade.php", $actionsContent);
    }

    protected function generateUiBuilder(Api $api): void
    {
        // Generate UI configuration
        $configContent = $this->getStub('ui/config.stub');
        $this->createFile("UI/{$api->name}Config.php", $configContent);

        // Generate UI components
        foreach ($api->ui_config['components'] ?? [] as $component) {
            $this->generateUiComponent($api, $component);
        }

        // Generate UI views
        $this->generateUiViews($api);

        // Generate UI styles
        $stylesContent = $this->getStub('ui/styles.stub');
        $this->createFile("resources/css/ui/{$api->name}.css", $stylesContent);
    }

    protected function generateUiComponent(Api $api, array $component): void
    {
        // Generate component class
        $classContent = $this->getStub("ui/components/{$component['type']}.stub");
        $this->createFile("Components/UI/{$component['name']}.php", $classContent);

        // Generate component view
        $viewContent = $this->getStub("ui/views/components/{$component['type']}.stub");
        $this->createFile("resources/views/components/ui/{$component['name']}.blade.php", $viewContent);

        // Generate component styles
        $stylesContent = $this->getStub("ui/styles/components/{$component['type']}.stub");
        $this->createFile("resources/css/ui/components/{$component['name']}.css", $stylesContent);
    }

    protected function generateUiViews(Api $api): void
    {
        // Generate UI layout
        $layoutContent = $this->getStub('ui/views/layout.stub');
        $this->createFile("resources/views/ui/{$api->name}/layout.blade.php", $layoutContent);

        // Generate UI sections
        foreach ($api->ui_config['sections'] ?? [] as $section) {
            $sectionContent = $this->getStub("ui/views/sections/{$section['type']}.stub");
            $this->createFile("resources/views/ui/{$api->name}/sections/{$section['name']}.blade.php", $sectionContent);
        }
    }

    protected function generateThemeBuilder(Api $api): void
    {
        // Generate theme configuration
        $configContent = $this->getStub('theme/config.stub');
        $this->createFile("Theme/{$api->name}Config.php", $configContent);

        // Generate theme styles
        $this->generateThemeStyles($api);

        // Generate theme components
        $this->generateThemeComponents($api);

        // Generate theme documentation
        $docsContent = $this->getStub('theme/documentation.stub');
        $this->createFile("resources/docs/theme/{$api->name}.md", $docsContent);
    }

    protected function generateThemeStyles(Api $api): void
    {
        // Generate base styles
        $baseContent = $this->getStub('theme/styles/base.stub');
        $this->createFile("resources/css/theme/{$api->name}/base.css", $baseContent);

        // Generate color scheme
        $colorsContent = $this->getStub('theme/styles/colors.stub');
        $this->createFile("resources/css/theme/{$api->name}/colors.css", $colorsContent);

        // Generate typography
        $typographyContent = $this->getStub('theme/styles/typography.stub');
        $this->createFile("resources/css/theme/{$api->name}/typography.css", $typographyContent);

        // Generate animations
        $animationsContent = $this->getStub('theme/styles/animations.stub');
        $this->createFile("resources/css/theme/{$api->name}/animations.css", $animationsContent);
    }

    protected function generateThemeComponents(Api $api): void
    {
        // Generate component styles
        foreach ($api->theme_config['components'] ?? [] as $component) {
            $componentContent = $this->getStub("theme/styles/components/{$component['type']}.stub");
            $this->createFile("resources/css/theme/{$api->name}/components/{$component['name']}.css", $componentContent);
        }
    }
} 