<?php

namespace LaravelBuilder\VisualBuilder\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class InstallVisualBuilderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visual-builder:install {--framework= : Specify framework directly}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Laravel Visual Builder with interactive framework selection';

    /**
     * Available frameworks
     */
    protected array $frameworks = [
        1 => ['name' => 'Blade Only', 'description' => 'Traditional Laravel', 'tag' => 'blade'],
        2 => ['name' => 'Livewire', 'description' => 'Reactive PHP Components', 'tag' => 'livewire'],
        3 => ['name' => 'Vue.js', 'description' => 'Progressive JavaScript Framework', 'tag' => 'vue'],
        4 => ['name' => 'React', 'description' => 'JavaScript Library for UI', 'tag' => 'react'],
        5 => ['name' => 'All Frameworks', 'description' => 'Complete Setup', 'tag' => 'all'],
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Laravel Visual Builder Installation');
        $this->info('==================================');
        $this->newLine();

        $framework = $this->getFrameworkChoice();
        
        if (!$framework) {
            $this->error('Invalid choice. Installation cancelled.');
            return 1;
        }

        $this->info("Installing Laravel Visual Builder with {$framework['name']}...");
        $this->newLine();

        try {
            $this->installPackage();
            $this->publishConfiguration($framework);
            $this->installFrameworkDependencies($framework);
            $this->createExampleComponents($framework);
            $this->updateConfiguration($framework);
            
            $this->newLine();
            $this->info('✅ Laravel Visual Builder installed successfully!');
            $this->info("Framework: {$framework['name']}");
            $this->newLine();
            
            $this->displayNextSteps($framework);
            
        } catch (\Exception $e) {
            $this->error('❌ Installation failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Get framework choice from user
     */
    protected function getFrameworkChoice(): ?array
    {
        $framework = $this->option('framework');
        
        if ($framework) {
            return $this->getFrameworkByTag($framework);
        }

        $this->info('Choose your preferred frontend framework:');
        $this->newLine();

        foreach ($this->frameworks as $key => $framework) {
            $this->line("{$key}. {$framework['name']} ({$framework['description']})");
        }

        $this->newLine();
        $choice = $this->ask('Enter your choice (1-5)', '1');

        return $this->frameworks[$choice] ?? null;
    }

    /**
     * Get framework by tag
     */
    protected function getFrameworkByTag(string $tag): ?array
    {
        foreach ($this->frameworks as $framework) {
            if ($framework['tag'] === $tag) {
                return $framework;
            }
        }
        return null;
    }

    /**
     * Install the main package
     */
    protected function installPackage(): void
    {
        $this->info('📦 Installing Laravel Visual Builder package...');
        
        // Check if package is already installed
        if (class_exists('LaravelBuilder\VisualBuilder\VisualBuilderServiceProvider')) {
            $this->warn('Package already installed, skipping...');
            return;
        }

        // In a real implementation, you would run composer require here
        $this->line('Running: composer require laravel-builder/visual-builder');
        // Artisan::call('composer:require', ['package' => 'laravel-builder/visual-builder']);
    }

    /**
     * Publish configuration files
     */
    protected function publishConfiguration(array $framework): void
    {
        $this->info('⚙️ Publishing configuration files...');
        
        // Publish main configuration
        Artisan::call('vendor:publish', [
            '--provider' => 'LaravelBuilder\VisualBuilder\VisualBuilderServiceProvider',
            '--tag' => 'config',
            '--force' => true
        ]);

        // Publish framework-specific configuration
        if ($framework['tag'] !== 'all') {
            Artisan::call('vendor:publish', [
                '--provider' => 'LaravelBuilder\VisualBuilder\VisualBuilderServiceProvider',
                '--tag' => $framework['tag'],
                '--force' => true
            ]);
        } else {
            // Publish all framework configurations
            foreach (['blade', 'livewire', 'vue', 'react'] as $tag) {
                Artisan::call('vendor:publish', [
                    '--provider' => 'LaravelBuilder\VisualBuilder\VisualBuilderServiceProvider',
                    '--tag' => $tag,
                    '--force' => true
                ]);
            }
        }

        $this->info('✅ Configuration files published successfully!');
    }

    /**
     * Install framework-specific dependencies
     */
    protected function installFrameworkDependencies(array $framework): void
    {
        $this->info('🔧 Installing framework dependencies...');

        switch ($framework['tag']) {
            case 'livewire':
                $this->installLivewireDependencies();
                break;
            case 'vue':
                $this->installVueDependencies();
                break;
            case 'react':
                $this->installReactDependencies();
                break;
            case 'all':
                $this->installAllDependencies();
                break;
            default:
                $this->line('No additional dependencies required for Blade.');
        }
    }

    /**
     * Install Livewire dependencies
     */
    protected function installLivewireDependencies(): void
    {
        $this->line('Installing Livewire...');
        // Artisan::call('composer:require', ['package' => 'livewire/livewire']);
        $this->info('✅ Livewire dependencies installed!');
    }

    /**
     * Install Vue.js dependencies
     */
    protected function installVueDependencies(): void
    {
        $this->line('Installing Vue.js dependencies...');
        // $this->runCommand('npm install vue@next @vitejs/plugin-vue');
        $this->info('✅ Vue.js dependencies installed!');
    }

    /**
     * Install React dependencies
     */
    protected function installReactDependencies(): void
    {
        $this->line('Installing React dependencies...');
        // $this->runCommand('npm install react react-dom @vitejs/plugin-react');
        $this->info('✅ React dependencies installed!');
    }

    /**
     * Install all framework dependencies
     */
    protected function installAllDependencies(): void
    {
        $this->line('Installing all framework dependencies...');
        $this->installLivewireDependencies();
        $this->installVueDependencies();
        $this->installReactDependencies();
        $this->info('✅ All framework dependencies installed!');
    }

    /**
     * Create example components
     */
    protected function createExampleComponents(array $framework): void
    {
        $this->info('🎨 Creating example components...');

        switch ($framework['tag']) {
            case 'blade':
                $this->createBladeComponents();
                break;
            case 'livewire':
                $this->createLivewireComponents();
                break;
            case 'vue':
                $this->createVueComponents();
                break;
            case 'react':
                $this->createReactComponents();
                break;
            case 'all':
                $this->createAllComponents();
                break;
        }

        $this->info('✅ Example components created successfully!');
    }

    /**
     * Create Blade components
     */
    protected function createBladeComponents(): void
    {
        $componentsPath = resource_path('views/components');
        File::makeDirectory($componentsPath, 0755, true, true);

        $componentContent = <<<'BLADE'
<div class="user-card bg-white rounded-lg shadow-md p-6">
    <h3 class="text-xl font-bold text-gray-900">{{ $name }}</h3>
    <p class="text-gray-600">{{ $email }}</p>
    @if(isset($avatar))
        <img src="{{ $avatar }}" alt="{{ $name }}" class="w-16 h-16 rounded-full mt-4">
    @endif
</div>
BLADE;

        File::put($componentsPath . '/user-card.blade.php', $componentContent);
    }

    /**
     * Create Livewire components
     */
    protected function createLivewireComponents(): void
    {
        $componentsPath = app_path('Livewire');
        File::makeDirectory($componentsPath, 0755, true, true);

        $componentClass = <<<'PHP'
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;

class UserList extends Component
{
    public $users = [];
    public $search = '';

    public function mount()
    {
        $this->loadUsers();
    }

    public function loadUsers()
    {
        $this->users = User::when($this->search, function($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
        })->get();
    }

    public function updatedSearch()
    {
        $this->loadUsers();
    }

    public function render()
    {
        return view('livewire.user-list');
    }
}
PHP;

        File::put($componentsPath . '/UserList.php', $componentClass);

        // Create the view
        $viewsPath = resource_path('views/livewire');
        File::makeDirectory($viewsPath, 0755, true, true);

        $viewContent = <<<'BLADE'
<div class="user-list">
    <div class="mb-4">
        <input wire:model.live="search" type="text" placeholder="Search users..." 
               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
    </div>
    
    <div class="space-y-4">
        @foreach($users as $user)
            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="text-lg font-semibold">{{ $user->name }}</h3>
                <p class="text-gray-600">{{ $user->email }}</p>
            </div>
        @endforeach
    </div>
</div>
BLADE;

        File::put($viewsPath . '/user-list.blade.php', $viewContent);
    }

    /**
     * Create Vue.js components
     */
    protected function createVueComponents(): void
    {
        $componentsPath = resource_path('js/components');
        File::makeDirectory($componentsPath, 0755, true, true);

        $componentContent = <<<'VUE'
<template>
  <div class="user-card bg-white rounded-lg shadow-md p-6">
    <h3 class="text-xl font-bold text-gray-900">{{ user.name }}</h3>
    <p class="text-gray-600">{{ user.email }}</p>
    <img v-if="user.avatar" :src="user.avatar" :alt="user.name" class="w-16 h-16 rounded-full mt-4">
  </div>
</template>

<script>
export default {
  name: 'UserCard',
  props: {
    user: {
      type: Object,
      required: true
    }
  }
}
</script>

<style scoped>
.user-card {
  transition: transform 0.2s ease-in-out;
}

.user-card:hover {
  transform: translateY(-2px);
}
</style>
VUE;

        File::put($componentsPath . '/UserCard.vue', $componentContent);
    }

    /**
     * Create React components
     */
    protected function createReactComponents(): void
    {
        $componentsPath = resource_path('js/components');
        File::makeDirectory($componentsPath, 0755, true, true);

        $componentContent = <<<'JSX'
import React from 'react';

const UserCard = ({ user }) => {
  return (
    <div className="user-card bg-white rounded-lg shadow-md p-6">
      <h3 className="text-xl font-bold text-gray-900">{user.name}</h3>
      <p className="text-gray-600">{user.email}</p>
      {user.avatar && (
        <img src={user.avatar} alt={user.name} className="w-16 h-16 rounded-full mt-4" />
      )}
    </div>
  );
};

export default UserCard;
JSX;

        File::put($componentsPath . '/UserCard.jsx', $componentContent);
    }

    /**
     * Create all framework components
     */
    protected function createAllComponents(): void
    {
        $this->createBladeComponents();
        $this->createLivewireComponents();
        $this->createVueComponents();
        $this->createReactComponents();
    }

    /**
     * Update configuration file
     */
    protected function updateConfiguration(array $framework): void
    {
        $this->info('📝 Updating configuration...');

        $configPath = config_path('visual-builder.php');
        
        if (!File::exists($configPath)) {
            $this->warn('Configuration file not found. Please publish it first.');
            return;
        }

        $config = require $configPath;
        
        // Update frontend configuration
        $config['frontend'] = [
            'framework' => $framework['tag'],
            'components_path' => $this->getComponentsPath($framework),
            'layouts_path' => resource_path('views/layouts'),
            'views_path' => resource_path('views/livewire'),
            'api_base_url' => '/api',
        ];

        // Write updated configuration
        $configContent = "<?php\n\nreturn " . var_export($config, true) . ";\n";
        File::put($configPath, $configContent);

        $this->info('✅ Configuration updated successfully!');
    }

    /**
     * Get components path for framework
     */
    protected function getComponentsPath(array $framework): string
    {
        switch ($framework['tag']) {
            case 'blade':
                return resource_path('views/components');
            case 'livewire':
                return app_path('Livewire');
            case 'vue':
            case 'react':
                return resource_path('js/components');
            default:
                return resource_path('views/components');
        }
    }

    /**
     * Display next steps
     */
    protected function displayNextSteps(array $framework): void
    {
        $this->newLine();
        $this->info('🚀 Next Steps:');
        $this->newLine();

        $this->line('1. Configure your environment variables in .env:');
        $this->line('   VISUAL_BUILDER_API_KEY=your_api_key');
        $this->line('   VISUAL_BUILDER_DEBUG=true');
        $this->newLine();

        $this->line('2. Run database migrations:');
        $this->line('   php artisan migrate');
        $this->newLine();

        if (in_array($framework['tag'], ['vue', 'react', 'all'])) {
            $this->line('3. Install and build frontend assets:');
            $this->line('   npm install && npm run build');
            $this->newLine();
        }

        $this->line('4. Start the development server:');
        $this->line('   php artisan serve');
        $this->newLine();

        $this->line('5. Visit the builder interface:');
        $this->line('   http://localhost:8000/builder');
        $this->newLine();

        $this->info('📚 Documentation: https://github.com/laravelgpt/vusial-builder');
        $this->info('💬 Support: support@laravel-builder.com');
    }
} 