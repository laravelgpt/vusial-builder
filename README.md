# Laravel Visual Builder

A comprehensive Laravel package for building and managing AI agents with advanced features, supporting Laravel 10, 11, and 12.

## Laravel Version Compatibility

| Feature                    | Laravel 10         | Laravel 11          | Laravel 12 (early/nightly)        |
| -------------------------- | ------------------ | ------------------- | --------------------------------- |
| PHP Version                | 8.1+               | 8.2+                | 8.2+ (likely 8.3+ soon)           |
| App Structure              | Traditional        | Minimal, modular    | Modular + Env-specific bootstraps |
| Feature Flags (Pennant)    | ✅                  | ✅                   | ✅                                 |
| Laravel Reverb (WebSocket) | Optional (preview) | ✅ (included)        | ✅ Improved                        |
| Laravel Volt (UI)          | ❌                  | ✅                   | ✅                                 |
| Route & Middleware Setup   | Traditional        | Via `bootstrap.php` | Improved modular setup            |
| Type Declarations          | Added              | Enforced            | Enforced                          |
| Laravel Prompts (CLI UX)   | ✅                  | ✅                   | Enhanced with AI                  |
| Livewire Support           | Optional           | Optional            | Optimized with Volt/Prism         |

## Features

### AI Agent Components
- **Chat Interface**: Real-time chat with AI agents
- **Error Handling**: Comprehensive error management and recovery
- **Prompt Engineering**: Advanced prompt management and optimization
- **Context Management**: Intelligent context handling for conversations
- **Response Processing**: Sophisticated response handling and formatting
- **WebSocket Support**: Real-time communication via Laravel Reverb
- **UI Components**: Modern UI with Laravel Volt integration
- **Feature Flags**: Dynamic feature toggling with Laravel Pennant
- **AI Model Integration**: Support for OpenAI, Anthropic, and custom models
- **Conversation History**: Persistent chat history with search capabilities
- **Multi-language Support**: AI responses in multiple languages
- **Voice Integration**: Text-to-speech and speech-to-text capabilities

### Security Features
- **Malware Detection**: Advanced malware scanning and analysis
- **Content Analysis**: Comprehensive content validation
- **File Validation**: Secure file type verification
- **Security Middleware**: Enhanced security layer
- **Quarantine System**: Safe file isolation
- **Rate Limiting**: Advanced rate limiting with Redis support
- **Two-Factor Authentication**: Enhanced security with 2FA
- **API Security**: JWT and OAuth2 integration
- **Audit Logging**: Comprehensive security audit trails
- **Vulnerability Scanning**: Automated security vulnerability detection

### Database Management
- **Backup System**: Automated database backups
- **Cloud Integration**: Google Drive and Mega.nz support
- **Backup Validation**: Secure backup verification
- **Scheduled Backups**: Automated backup scheduling
- **Backup Notifications**: Real-time backup status updates
- **Database Migration Builder**: Visual migration creation
- **Seed Data Management**: Automated seed data generation
- **Database Optimization**: Performance optimization tools
- **Query Builder**: Visual query builder interface
- **Database Monitoring**: Real-time database performance monitoring

### Information Management
- **System Information**: Detailed system status monitoring
- **Cloud Storage**: Multi-provider cloud storage support
- **Backup Management**: Comprehensive backup handling
- **Data Synchronization**: Real-time data sync across platforms
- **Storage Analytics**: Detailed storage usage statistics
- **Performance Monitoring**: Application performance tracking
- **Error Tracking**: Comprehensive error logging and analysis
- **User Analytics**: Detailed user behavior analytics
- **API Analytics**: API usage and performance metrics
- **Resource Monitoring**: Server resource utilization tracking

### Builder Components
- **Dashboard Builder**: Custom dashboard creation
- **Report Builder**: Advanced reporting system
- **Export Builder**: Data export functionality
- **Import Builder**: Data import capabilities
- **Notification Builder**: Custom notification system
- **Event Builder**: Event management system
- **Command Builder**: Custom command creation
- **Job Builder**: Background job management
- **Mail Builder**: Email system builder
- **Resource Builder**: API resource management
- **Controller Builder**: Controller generation
- **Security Builder**: Security component builder
- **Model Builder**: Eloquent model generation
- **Migration Builder**: Database migration creation
- **Seeder Builder**: Database seeder generation
- **Policy Builder**: Authorization policy creation
- **Middleware Builder**: Custom middleware generation
- **Route Builder**: Route definition and management
- **View Builder**: Blade template generation
- **Test Builder**: Automated test generation

### Frontend Framework Support
- **Blade Templates**: Native Laravel Blade support
- **Livewire Components**: Real-time reactive components
- **Vue.js Integration**: Vue.js component builder
- **React Integration**: React component builder
- **Alpine.js Support**: Lightweight JavaScript framework
- **Tailwind CSS**: Utility-first CSS framework
- **Bootstrap**: Bootstrap component library
- **Custom CSS**: Custom styling support
- **Responsive Design**: Mobile-first responsive layouts
- **Dark Mode**: Built-in dark mode support

## Installation

### Quick Installation

```bash
composer require laravel-builder/visual-builder
```

### Interactive Installation

Run the interactive installer to choose your preferred frontend framework:

```bash
php artisan visual-builder:install
```

This will present you with installation options:

```
Laravel Visual Builder Installation
==================================

Choose your preferred frontend framework:

1. Blade Only (Traditional Laravel)
2. Livewire (Reactive PHP Components)
3. Vue.js (Progressive JavaScript Framework)
4. React (JavaScript Library for UI)
5. All Frameworks (Complete Setup)

Enter your choice (1-5): 
```

### Framework-Specific Installation

#### Blade Only (Traditional Laravel)
```bash
composer require laravel-builder/visual-builder
php artisan vendor:publish --provider="LaravelBuilder\VisualBuilder\VisualBuilderServiceProvider" --tag="blade"
```

#### Livewire Integration
```bash
composer require laravel-builder/visual-builder
composer require livewire/livewire
php artisan vendor:publish --provider="LaravelBuilder\VisualBuilder\VisualBuilderServiceProvider" --tag="livewire"
```

#### Vue.js Integration
```bash
composer require laravel-builder/visual-builder
npm install vue@next @vitejs/plugin-vue
php artisan vendor:publish --provider="LaravelBuilder\VisualBuilder\VisualBuilderServiceProvider" --tag="vue"
```

#### React Integration
```bash
composer require laravel-builder/visual-builder
npm install react react-dom @vitejs/plugin-react
php artisan vendor:publish --provider="LaravelBuilder\VisualBuilder\VisualBuilderServiceProvider" --tag="react"
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="LaravelBuilder\VisualBuilder\VisualBuilderServiceProvider"
```

### Laravel 11+ Setup

For Laravel 11 and above, the package uses the new modular structure:

```php
// bootstrap/app.php
return Application::configure(basePath: dirname(__DIR__))
    ->withProviders()
    ->withMiddleware([
        // Your middleware configuration
    ])
    ->create();
```

### Frontend Framework Configuration

#### Blade Configuration
```php
// config/visual-builder.php
'frontend' => [
    'framework' => 'blade',
    'components_path' => resource_path('views/components'),
    'layouts_path' => resource_path('views/layouts'),
],
```

#### Livewire Configuration
```php
// config/visual-builder.php
'frontend' => [
    'framework' => 'livewire',
    'components_path' => app_path('Livewire'),
    'views_path' => resource_path('views/livewire'),
],
```

#### Vue.js Configuration
```php
// config/visual-builder.php
'frontend' => [
    'framework' => 'vue',
    'components_path' => resource_path('js/components'),
    'api_base_url' => '/api',
],
```

#### React Configuration
```php
// config/visual-builder.php
'frontend' => [
    'framework' => 'react',
    'components_path' => resource_path('js/components'),
    'api_base_url' => '/api',
],
```

### Feature Flags (Pennant)

```php
use Laravel\Pennant\Feature;

// Define features
Feature::define('ai-chat', function () {
    return true;
});

// Check features
if (Feature::active('ai-chat')) {
    // Enable AI chat functionality
}
```

### WebSocket Setup (Reverb)

```php
// config/reverb.php
return [
    'servers' => [
        'default' => [
            'host' => env('REVERB_HOST', '127.0.0.1'),
            'port' => env('REVERB_PORT', 8080),
        ],
    ],
];
```

### Volt Components

```php
use Laravel\Volt\Volt;

Volt::component('ai-chat', function () {
    return view('components.ai-chat');
});
```

## Usage

### AI Agent Setup

```php
use LaravelBuilder\VisualBuilder\Services\AiAgentBuilder;

$builder = new AiAgentBuilder();
$builder->build('YourModel');
```

### Security Implementation

```php
use LaravelBuilder\VisualBuilder\Services\SecurityBuilder;

$builder = new SecurityBuilder();
$builder->build('YourModel');
```

### Database Backup

```php
use LaravelBuilder\VisualBuilder\Services\DatabaseBackupBuilder;

$builder = new DatabaseBackupBuilder();
$builder->build('YourModel');
```

### Frontend Component Usage

#### Blade Component
```php
// resources/views/components/user-card.blade.php
<div class="user-card">
    <h3>{{ $name }}</h3>
    <p>{{ $email }}</p>
</div>

// Usage
<x-user-card name="John Doe" email="john@example.com" />
```

#### Livewire Component
```php
// app/Livewire/UserList.php
class UserList extends Component
{
    public $users = [];

    public function mount()
    {
        $this->users = User::all();
    }

    public function render()
    {
        return view('livewire.user-list');
    }
}

// Usage
<livewire:user-list />
```

#### Vue.js Component
```vue
<!-- resources/js/components/UserCard.vue -->
<template>
  <div class="user-card">
    <h3>{{ user.name }}</h3>
    <p>{{ user.email }}</p>
  </div>
</template>

<script>
export default {
  props: ['user']
}
</script>
```

#### React Component
```jsx
// resources/js/components/UserCard.jsx
import React from 'react';

const UserCard = ({ user }) => {
  return (
    <div className="user-card">
      <h3>{user.name}</h3>
      <p>{user.email}</p>
    </div>
  );
};

export default UserCard;
```

## Cloud Storage Integration

### Google Drive

```php
use LaravelBuilder\VisualBuilder\Services\CloudStorageService;

$storage = new CloudStorageService('google');
$storage->connect();
```

### Mega.nz

```php
use LaravelBuilder\VisualBuilder\Services\CloudStorageService;

$storage = new CloudStorageService('mega');
$storage->connect();
```

### Amazon S3

```php
use LaravelBuilder\VisualBuilder\Services\CloudStorageService;

$storage = new CloudStorageService('s3');
$storage->connect();
```

### Dropbox

```php
use LaravelBuilder\VisualBuilder\Services\CloudStorageService;

$storage = new CloudStorageService('dropbox');
$storage->connect();
```

## Security Features

### Malware Scanning

```php
use LaravelBuilder\VisualBuilder\Services\SecurityService;

$security = new SecurityService();
$result = $security->scanFile($file);
```

### Content Analysis

```php
use LaravelBuilder\VisualBuilder\Services\ContentAnalyzer;

$analyzer = new ContentAnalyzer();
$result = $analyzer->analyze($content);
```

### Rate Limiting

```php
use LaravelBuilder\VisualBuilder\Services\RateLimiter;

$limiter = new RateLimiter();
$limiter->throttle('api', 60, 100); // 100 requests per minute
```

## Database Backup

### Automated Backups

```php
use LaravelBuilder\VisualBuilder\Services\BackupService;

$backup = new BackupService();
$backup->create();
```

### Backup Validation

```php
use LaravelBuilder\VisualBuilder\Services\BackupService;

$backup = new BackupService();
$isValid = $backup->validate($backupFile);
```

### Scheduled Backups

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('backup:database')->daily();
}
```

## Information Management

### System Information

```php
use LaravelBuilder\VisualBuilder\Services\InformationService;

$info = new InformationService();
$systemInfo = $info->getSystemInfo();
```

### Storage Analytics

```php
use LaravelBuilder\VisualBuilder\Services\InformationService;

$info = new InformationService();
$storageInfo = $info->getStorageInfo();
```

### Performance Monitoring

```php
use LaravelBuilder\VisualBuilder\Services\PerformanceMonitor;

$monitor = new PerformanceMonitor();
$metrics = $monitor->getMetrics();
```

## Testing

```bash
composer test
```

### Framework-Specific Testing

#### Blade Testing
```bash
php artisan test --filter=BladeComponentTest
```

#### Livewire Testing
```bash
php artisan test --filter=LivewireComponentTest
```

#### Vue.js Testing
```bash
npm run test:vue
```

#### React Testing
```bash
npm run test:react
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email support@laravelbuilder.com instead of using the issue tracker.

## Credits

- [Laravel Builder](https://github.com/laravel-builder)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Support

For support, please open an issue in the GitHub repository or contact us at support@laravel-builder.com.