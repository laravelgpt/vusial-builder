<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;

class InstallPackageCommand extends Command
{
    protected $signature = 'package:install 
        {framework? : The framework to install (blade, livewire, vue, react)} 
        {--laravel= : The Laravel version (10, 11, 12)} 
        {--force : Force installation even if a newer version is available}
        {--recheck : Recheck and fix builder features}
        {--update : Update all features to latest version}';

    protected $description = 'Install the package and its dependencies';

    protected $features = [
        'builder' => [
            'components' => [
                'accordion',
                'alert',
                'button',
                'card',
                'form',
                'tabs',
                'toast'
            ],
            'frameworks' => [
                'blade',
                'livewire',
                'vue',
                'react'
            ]
        ],
        'authentication' => [
            'social' => [
                'google',
                'github',
                'facebook',
                'twitter'
            ],
            'methods' => [
                'session',
                'token',
                'sanctum'
            ]
        ],
        'ui' => [
            'themes' => [
                'light',
                'dark'
            ],
            'components' => [
                'shadcn',
                'heroicons'
            ]
        ]
    ];

    public function handle()
    {
        // Recheck and fix features if requested
        if ($this->option('recheck')) {
            $this->recheckFeatures();
            return;
        }

        // Update all features if requested
        if ($this->option('update')) {
            $this->updateAllFeatures();
            return;
        }

        // Laravel version selection
        $laravelVersion = $this->option('laravel') ?? $this->askLaravelVersion();

        // Check for updates
        if (!$this->option('force') && $this->checkForUpdates()) {
            if ($this->confirm('A newer version is available. Would you like to update first?')) {
                $this->updatePackage($laravelVersion);
                return;
            }
        }

        $framework = $this->argument('framework') ?? $this->choice(
            'Which framework would you like to install?',
            $this->features['builder']['frameworks'],
            'blade'
        );

        $this->info("Installing package for Laravel {$laravelVersion} and {$framework}...");

        // Install required dependencies
        $this->installDependencies($framework, $laravelVersion);

        // Publish configuration
        $this->publishConfiguration();

        // Publish assets
        $this->publishAssets();

        // Update .env file
        $this->updateEnvironmentFile($framework, $laravelVersion);

        // Run post-installation tasks
        $this->runPostInstallTasks($framework);

        // Verify installation
        $this->verifyInstallation($framework, $laravelVersion);

        $this->info("Package installed successfully for Laravel {$laravelVersion}!");
        $this->info('Please run `php artisan package:configure` to configure the package.');
    }

    protected function recheckFeatures(): void
    {
        $this->info('Rechecking and fixing builder features...');

        // Check builder components
        foreach ($this->features['builder']['components'] as $component) {
            $this->checkComponent($component);
        }

        // Check authentication features
        foreach ($this->features['authentication']['social'] as $provider) {
            $this->checkSocialAuth($provider);
        }

        // Check UI components
        foreach ($this->features['ui']['components'] as $component) {
            $this->checkUIComponent($component);
        }

        // Verify configuration
        $this->verifyConfiguration();

        $this->info('Feature recheck completed!');
    }

    protected function updateAllFeatures(): void
    {
        $this->info('Updating all features to latest version...');

        // Update builder components
        foreach ($this->features['builder']['components'] as $component) {
            $this->updateComponent($component);
        }

        // Update authentication features
        foreach ($this->features['authentication']['social'] as $provider) {
            $this->updateSocialAuth($provider);
        }

        // Update UI components
        foreach ($this->features['ui']['components'] as $component) {
            $this->updateUIComponent($component);
        }

        // Clear caches
        $this->clearCaches();

        $this->info('All features updated successfully!');
    }

    protected function checkComponent(string $component): void
    {
        $this->info("Checking {$component} component...");

        // Check component files
        $componentPath = resource_path("views/components/{$component}");
        if (!File::exists($componentPath)) {
            $this->warn("{$component} component files missing. Recreating...");
            $this->recreateComponent($component);
        }

        // Check component registration
        $this->verifyComponentRegistration($component);
    }

    protected function checkSocialAuth(string $provider): void
    {
        $this->info("Checking {$provider} authentication...");

        // Check provider configuration
        $configPath = config_path("services.php");
        if (!File::exists($configPath) || !$this->hasProviderConfig($provider)) {
            $this->warn("{$provider} configuration missing. Adding...");
            $this->addProviderConfig($provider);
        }

        // Check provider routes
        $this->verifyProviderRoutes($provider);
    }

    protected function checkUIComponent(string $component): void
    {
        $this->info("Checking {$component} UI component...");

        // Check component assets
        $assetPath = public_path("vendor/package/{$component}");
        if (!File::exists($assetPath)) {
            $this->warn("{$component} assets missing. Reinstalling...");
            $this->reinstallUIComponent($component);
        }

        // Check component configuration
        $this->verifyUIComponentConfig($component);
    }

    protected function verifyInstallation(string $framework, string $laravelVersion): void
    {
        $this->info('Verifying installation...');

        // Verify framework installation
        $this->verifyFrameworkInstallation($framework);

        // Verify Laravel version compatibility
        $this->verifyLaravelCompatibility($laravelVersion);

        // Verify component registration
        foreach ($this->features['builder']['components'] as $component) {
            $this->verifyComponentRegistration($component);
        }

        // Verify configuration
        $this->verifyConfiguration();

        $this->info('Installation verification completed!');
    }

    protected function verifyFrameworkInstallation(string $framework): void
    {
        switch ($framework) {
            case 'livewire':
                if (!class_exists('Livewire\Livewire')) {
                    $this->error('Livewire not properly installed. Reinstalling...');
                    $this->call('livewire:install');
                }
                break;
            case 'vue':
                if (!File::exists(base_path('package.json'))) {
                    $this->error('Vue.js not properly installed. Reinstalling...');
                    $this->call('npm', ['install', 'vue']);
                }
                break;
            case 'react':
                if (!File::exists(base_path('package.json'))) {
                    $this->error('React not properly installed. Reinstalling...');
                    $this->call('npm', ['install', 'react', 'react-dom']);
                }
                break;
        }
    }

    protected function verifyLaravelCompatibility(string $laravelVersion): void
    {
        $currentVersion = app()->version();
        if (!str_starts_with($currentVersion, $laravelVersion)) {
            $this->warn("Warning: Current Laravel version ({$currentVersion}) doesn't match selected version ({$laravelVersion})");
        }
    }

    protected function verifyConfiguration(): void
    {
        $configPath = config_path('package.php');
        if (!File::exists($configPath)) {
            $this->error('Package configuration missing. Recreating...');
            $this->publishConfiguration();
        }

        // Verify required configuration values
        $config = config('package');
        $requiredKeys = ['framework', 'components', 'styles', 'assets'];
        foreach ($requiredKeys as $key) {
            if (!isset($config[$key])) {
                $this->error("Missing required configuration key: {$key}");
                $this->recreateConfiguration();
                break;
            }
        }
    }

    protected function clearCaches(): void
    {
        $this->info('Clearing caches...');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
    }

    protected function askLaravelVersion()
    {
        return $this->choice(
            'Which Laravel version are you using?',
            ['10', '11', '12'],
            '10'
        );
    }

    protected function checkForUpdates(): bool
    {
        try {
            $currentVersion = config('package.assets.version', '1.0.0');
            $response = Http::get('https://api.github.com/repos/your-username/your-package/releases/latest');
            
            if ($response->successful()) {
                $latestVersion = $response->json()['tag_name'];
                return version_compare($latestVersion, $currentVersion, '>');
            }
        } catch (\Exception $e) {
            $this->warn('Could not check for updates: ' . $e->getMessage());
        }

        return false;
    }

    protected function updatePackage(string $laravelVersion): void
    {
        $this->info('Updating package...');
        
        // Backup current configuration
        $this->backupConfiguration();
        
        // Update package via composer
        $this->call('composer', ['update', 'your-package-name']);
        
        // Restore configuration
        $this->restoreConfiguration();
        
        $this->info('Package updated successfully!');
    }

    protected function backupConfiguration(): void
    {
        $configPath = config_path('package.php');
        if (File::exists($configPath)) {
            File::copy($configPath, $configPath . '.backup');
        }
    }

    protected function restoreConfiguration(): void
    {
        $configPath = config_path('package.php');
        $backupPath = $configPath . '.backup';
        
        if (File::exists($backupPath)) {
            File::copy($backupPath, $configPath);
            File::delete($backupPath);
        }
    }

    protected function runPostInstallTasks(string $framework): void
    {
        $this->info('Running post-installation tasks...');

        // Clear caches
        $this->call('config:clear');
        $this->call('view:clear');
        $this->call('cache:clear');

        // Framework-specific tasks
        switch ($framework) {
            case 'livewire':
                $this->call('livewire:publish', ['--config' => true]);
                break;
            case 'vue':
                $this->call('npm', ['install']);
                break;
            case 'react':
                $this->call('npm', ['install']);
                break;
        }

        // Optimize for production if needed
        if ($this->confirm('Would you like to optimize for production?')) {
            $this->call('config:cache');
            $this->call('view:cache');
            $this->call('route:cache');
        }
    }

    protected function installDependencies(string $framework, string $laravelVersion)
    {
        $dependencies = config("package.dependencies.{$framework}");

        // Determine package version for Laravel
        $packageVersion = $this->getPackageVersionForLaravel($laravelVersion);
        $packageName = 'your-package-name';

        $this->info("Installing {$packageName} version {$packageVersion} for Laravel {$laravelVersion}...");
        $this->call('composer', ['require', "{$packageName}:{$packageVersion}"]);

        if (!empty($dependencies['required'])) {
            $this->info('Installing required dependencies...');
            foreach ($dependencies['required'] as $package) {
                $this->call('composer', ['require', $package]);
            }
        }

        if (!empty($dependencies['optional'])) {
            if ($this->confirm('Would you like to install optional dependencies?')) {
                foreach ($dependencies['optional'] as $package) {
                    $this->call('composer', ['require', $package]);
                }
            }
        }
    }

    protected function getPackageVersionForLaravel(string $laravelVersion): string
    {
        // Map Laravel version to package version
        switch ($laravelVersion) {
            case '12':
                return '^3.0';
            case '11':
                return '^2.0';
            case '10':
            default:
                return '^1.0';
        }
    }

    protected function publishConfiguration()
    {
        $this->info('Publishing configuration...');
        $this->call('vendor:publish', [
            '--provider' => 'App\Providers\PackageServiceProvider',
            '--tag' => 'config',
        ]);
    }

    protected function publishAssets()
    {
        $this->info('Publishing assets...');
        $this->call('vendor:publish', [
            '--provider' => 'App\Providers\PackageServiceProvider',
            '--tag' => 'assets',
        ]);
    }

    protected function updateEnvironmentFile(string $framework, string $laravelVersion)
    {
        $this->info('Updating environment file...');

        $envFile = base_path('.env');
        $envContent = File::get($envFile);

        // Update framework setting
        $envContent = preg_replace(
            '/PACKAGE_FRAMEWORK=.*/',
            "PACKAGE_FRAMEWORK={$framework}",
            $envContent
        );
        // Add framework setting if it doesn't exist
        if (!str_contains($envContent, 'PACKAGE_FRAMEWORK=')) {
            $envContent .= "\nPACKAGE_FRAMEWORK={$framework}";
        }

        // Update Laravel version
        $envContent = preg_replace(
            '/LARAVEL_VERSION=.*/',
            "LARAVEL_VERSION={$laravelVersion}",
            $envContent
        );
        if (!str_contains($envContent, 'LARAVEL_VERSION=')) {
            $envContent .= "\nLARAVEL_VERSION={$laravelVersion}";
        }

        File::put($envFile, $envContent);
    }
} 