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

### Security Features
- **Malware Detection**: Advanced malware scanning and analysis
- **Content Analysis**: Comprehensive content validation
- **File Validation**: Secure file type verification
- **Security Middleware**: Enhanced security layer
- **Quarantine System**: Safe file isolation

### Database Management
- **Backup System**: Automated database backups
- **Cloud Integration**: Google Drive and Mega.nz support
- **Backup Validation**: Secure backup verification
- **Scheduled Backups**: Automated backup scheduling
- **Backup Notifications**: Real-time backup status updates

### Information Management
- **System Information**: Detailed system status monitoring
- **Cloud Storage**: Multi-provider cloud storage support
- **Backup Management**: Comprehensive backup handling
- **Data Synchronization**: Real-time data sync across platforms
- **Storage Analytics**: Detailed storage usage statistics

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

## Installation

```bash
composer require laravel-builder/visual-builder
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

## Testing

```bash
composer test
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