<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class BuilderSetupCommand extends Command
{
    protected $signature = 'builder:setup 
        {--force : Force setup even if already installed}
        {--skip-migrations : Skip running migrations}
        {--skip-seeds : Skip running seeders}
        {--skip-assets : Skip publishing assets}';

    protected $description = 'Setup all builder features and components';

    protected $builders = [
        'page' => [
            'name' => 'Page Builder',
            'provider' => 'App\\Providers\\PageBuilderServiceProvider',
            'config' => 'page-builder.php',
            'migrations' => true,
            'seeds' => true,
            'assets' => true,
        ],
        'menu' => [
            'name' => 'Menu Builder',
            'provider' => 'App\\Providers\\MenuBuilderServiceProvider',
            'config' => 'menu-builder.php',
            'migrations' => true,
            'seeds' => true,
            'assets' => true,
        ],
        'route' => [
            'name' => 'Route Builder',
            'provider' => 'App\\Providers\\RouteBuilderServiceProvider',
            'config' => 'route-builder.php',
            'migrations' => false,
            'seeds' => false,
            'assets' => true,
        ],
        'controller' => [
            'name' => 'Controller Builder',
            'provider' => 'App\\Providers\\ControllerBuilderServiceProvider',
            'config' => 'controller-builder.php',
            'migrations' => false,
            'seeds' => false,
            'assets' => true,
        ],
        'model' => [
            'name' => 'Model Builder',
            'provider' => 'App\\Providers\\ModelBuilderServiceProvider',
            'config' => 'model-builder.php',
            'migrations' => false,
            'seeds' => false,
            'assets' => true,
        ],
        'migration' => [
            'name' => 'Migration Builder',
            'provider' => 'App\\Providers\\MigrationBuilderServiceProvider',
            'config' => 'migration-builder.php',
            'migrations' => false,
            'seeds' => false,
            'assets' => true,
        ],
        'seeder' => [
            'name' => 'Seeder Builder',
            'provider' => 'App\\Providers\\SeederBuilderServiceProvider',
            'config' => 'seeder-builder.php',
            'migrations' => false,
            'seeds' => false,
            'assets' => true,
        ],
        'component' => [
            'name' => 'Component Builder',
            'provider' => 'App\\Providers\\ComponentBuilderServiceProvider',
            'config' => 'component-builder.php',
            'migrations' => false,
            'seeds' => false,
            'assets' => true,
        ],
        'layout' => [
            'name' => 'Layout Builder',
            'provider' => 'App\\Providers\\LayoutBuilderServiceProvider',
            'config' => 'layout-builder.php',
            'migrations' => true,
            'seeds' => true,
            'assets' => true,
        ],
        'auth' => [
            'name' => 'Auth Builder',
            'provider' => 'App\\Providers\\AuthBuilderServiceProvider',
            'config' => 'auth-builder.php',
            'migrations' => true,
            'seeds' => true,
            'assets' => true,
        ],
        'api' => [
            'name' => 'API Builder',
            'provider' => 'App\\Providers\\ApiBuilderServiceProvider',
            'config' => 'api-builder.php',
            'migrations' => true,
            'seeds' => true,
            'assets' => true,
        ],
        'notification' => [
            'name' => 'Notification Builder',
            'provider' => 'App\\Providers\\NotificationBuilderServiceProvider',
            'config' => 'notification-builder.php',
            'migrations' => true,
            'seeds' => true,
            'assets' => true,
        ],
        'job' => [
            'name' => 'Job Builder',
            'provider' => 'App\\Providers\\JobBuilderServiceProvider',
            'config' => 'job-builder.php',
            'migrations' => true,
            'seeds' => false,
            'assets' => true,
        ],
        'event' => [
            'name' => 'Event Builder',
            'provider' => 'App\\Providers\\EventBuilderServiceProvider',
            'config' => 'event-builder.php',
            'migrations' => false,
            'seeds' => false,
            'assets' => true,
        ],
        'policy' => [
            'name' => 'Policy Builder',
            'provider' => 'App\\Providers\\PolicyBuilderServiceProvider',
            'config' => 'policy-builder.php',
            'migrations' => false,
            'seeds' => false,
            'assets' => true,
        ],
        'form' => [
            'name' => 'Form Builder',
            'provider' => 'App\\Providers\\FormBuilderServiceProvider',
            'config' => 'form-builder.php',
            'migrations' => true,
            'seeds' => true,
            'assets' => true,
        ],
        'module' => [
            'name' => 'Module Builder',
            'provider' => 'App\\Providers\\ModuleBuilderServiceProvider',
            'config' => 'module-builder.php',
            'migrations' => true,
            'seeds' => true,
            'assets' => true,
        ],
        'command' => [
            'name' => 'Command Builder',
            'provider' => 'App\\Providers\\CommandBuilderServiceProvider',
            'config' => 'command-builder.php',
            'migrations' => false,
            'seeds' => false,
            'assets' => true,
        ],
        'service' => [
            'name' => 'Service Provider Builder',
            'provider' => 'App\\Providers\\ServiceProviderBuilderServiceProvider',
            'config' => 'service-builder.php',
            'migrations' => false,
            'seeds' => false,
            'assets' => true,
        ],
        'config' => [
            'name' => 'Config Builder',
            'provider' => 'App\\Providers\\ConfigBuilderServiceProvider',
            'config' => 'config-builder.php',
            'migrations' => false,
            'seeds' => false,
            'assets' => true,
        ],
        'widget' => [
            'name' => 'Widget Builder',
            'provider' => 'App\\Providers\\WidgetBuilderServiceProvider',
            'config' => 'widget-builder.php',
            'migrations' => true,
            'seeds' => true,
            'assets' => true,
        ],
        'theme' => [
            'name' => 'Theme Builder',
            'provider' => 'App\\Providers\\ThemeBuilderServiceProvider',
            'config' => 'theme-builder.php',
            'migrations' => true,
            'seeds' => true,
            'assets' => true,
        ],
    ];

    public function handle()
    {
        $this->info('Starting builder setup...');

        foreach ($this->builders as $key => $builder) {
            $this->setupBuilder($key, $builder);
        }

        $this->info('Builder setup completed!');
    }

    protected function setupBuilder(string $key, array $builder): void
    {
        $this->info("Setting up {$builder['name']}...");

        // Publish configuration
        $this->publishConfig($builder);

        // Run migrations
        if (!$this->option('skip-migrations') && $builder['migrations']) {
            $this->runMigrations($builder);
        }

        // Run seeders
        if (!$this->option('skip-seeds') && $builder['seeds']) {
            $this->runSeeders($builder);
        }

        // Publish assets
        if (!$this->option('skip-assets') && $builder['assets']) {
            $this->publishAssets($builder);
        }

        // Register provider
        $this->registerProvider($builder);

        $this->info("{$builder['name']} setup completed!");
    }

    protected function publishConfig(array $builder): void
    {
        $this->info("Publishing {$builder['name']} configuration...");
        Artisan::call('vendor:publish', [
            '--provider' => $builder['provider'],
            '--tag' => 'config',
            '--force' => $this->option('force'),
        ]);
    }

    protected function runMigrations(array $builder): void
    {
        $this->info("Running {$builder['name']} migrations...");
        Artisan::call('migrate', [
            '--path' => "database/migrations/{$builder['name']}",
            '--force' => $this->option('force'),
        ]);
    }

    protected function runSeeders(array $builder): void
    {
        $this->info("Running {$builder['name']} seeders...");
        Artisan::call('db:seed', [
            '--class' => "{$builder['name']}Seeder",
            '--force' => $this->option('force'),
        ]);
    }

    protected function publishAssets(array $builder): void
    {
        $this->info("Publishing {$builder['name']} assets...");
        Artisan::call('vendor:publish', [
            '--provider' => $builder['provider'],
            '--tag' => 'assets',
            '--force' => $this->option('force'),
        ]);
    }

    protected function registerProvider(array $builder): void
    {
        $this->info("Registering {$builder['name']} provider...");
        $provider = $builder['provider'];
        if (class_exists($provider)) {
            app()->register($provider);
        }
    }
} 