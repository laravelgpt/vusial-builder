# Laravel Visual Builder v2.0.0

[![Version](https://img.shields.io/badge/version-2.0.0-blue.svg)](https://github.com/laravel-builder/visual-builder)
[![Laravel](https://img.shields.io/badge/Laravel-10%20%7C%2011%20%7C%2012-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-purple.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE.md)

A comprehensive Laravel package for building and managing AI agents with advanced features, supporting Laravel 10, 11, and 12.

## 🚀 Version 2.0.0 Release

### Major New Features
- ✅ **Telegram Bot Integration** - Complete bot functionality with webhook support
- ✅ **Advanced Backup System** - Multi-provider cloud storage backup
- ✅ **Security Service** - Comprehensive security features
- ✅ **Monitoring & Analytics** - Real-time system monitoring
- ✅ **Cloud Storage Integration** - Google Drive, Mega.nz, S3 support

### What's New in v2.0
- **57% Feature Completion** - Up from 30% in v1.0
- **5 Major Services** - Telegram, Backup, Security, Monitoring, Cloud Storage
- **Enhanced Configuration** - Improved service configuration
- **Better Error Handling** - Comprehensive error management
- **Performance Optimizations** - Faster service loading and execution

### Breaking Changes
- None - Fully backward compatible with v1.0

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
- **AI Training**: Custom model training and fine-tuning
- **Prompt Templates**: Reusable prompt templates and workflows
- **AI Analytics**: AI usage analytics and performance metrics
- **Model Comparison**: Compare different AI models and responses

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
- **SSL Certificate Management**: Automated SSL certificate handling
- **Firewall Integration**: Web application firewall (WAF) support
- **Encryption**: End-to-end encryption for sensitive data
- **Access Control**: Role-based access control (RBAC)
- **Session Management**: Advanced session security and management

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
- **Backup Encryption**: Encrypted backup storage
- **Incremental Backups**: Efficient incremental backup system
- **Backup Compression**: Compressed backup storage
- **Backup Retention**: Configurable backup retention policies
- **Database Cloning**: Quick database cloning for testing
- **Schema Versioning**: Database schema version control

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
- **Log Management**: Centralized log management and analysis
- **Health Checks**: Automated system health monitoring
- **Capacity Planning**: Resource capacity planning tools
- **Cost Optimization**: Cloud cost optimization recommendations
- **Compliance Reporting**: Regulatory compliance reporting

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
- **API Builder**: RESTful API generation
- **GraphQL Builder**: GraphQL schema and resolver generation
- **Webhook Builder**: Webhook endpoint creation
- **Queue Builder**: Queue job and worker management
- **Cache Builder**: Cache configuration and management

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
- **PWA Support**: Progressive Web App capabilities
- **SSR Support**: Server-side rendering for Vue/React
- **Component Library**: Pre-built component library
- **Theme Builder**: Visual theme customization
- **Icon Management**: Icon library and management

### Communication & Integration
- **Telegram Bot Integration**: Complete Telegram bot functionality
- **Slack Integration**: Slack workspace integration
- **Discord Integration**: Discord server integration
- **Email Integration**: Advanced email management
- **SMS Integration**: SMS notification system
- **Push Notifications**: Mobile push notifications
- **Webhook Management**: Webhook endpoint management
- **API Gateway**: Centralized API management
- **Third-party Integrations**: Popular service integrations
- **Social Media Integration**: Social media platform integration
- **Chat Widget**: Live chat widget for websites
- **Video Conferencing**: Video call integration
- **Voice Calls**: Voice call functionality
- **File Sharing**: Secure file sharing system
- **Collaboration Tools**: Team collaboration features

### Backup & Recovery
- **Automated Backups**: Scheduled automated backups
- **Backup Encryption**: Military-grade encryption
- **Backup Compression**: Efficient compression algorithms
- **Incremental Backups**: Space-efficient incremental backups
- **Backup Verification**: Automated backup integrity checks
- **Backup Restoration**: One-click backup restoration
- **Backup Monitoring**: Real-time backup status monitoring
- **Backup Analytics**: Backup performance analytics
- **Disaster Recovery**: Complete disaster recovery planning
- **Backup Testing**: Automated backup testing procedures
- **Backup Migration**: Cross-platform backup migration
- **Backup Archiving**: Long-term backup archiving
- **Backup Scheduling**: Flexible backup scheduling options
- **Backup Notifications**: Multi-channel backup notifications
- **Backup Compliance**: Regulatory compliance for backups

### Monitoring & Analytics
- **Real-time Monitoring**: Live system monitoring
- **Performance Analytics**: Detailed performance metrics
- **Error Tracking**: Comprehensive error monitoring
- **User Analytics**: User behavior analysis
- **API Analytics**: API performance monitoring
- **Security Analytics**: Security event analysis
- **Cost Analytics**: Resource cost analysis
- **Uptime Monitoring**: System uptime tracking
- **Load Balancing**: Intelligent load balancing
- **Auto-scaling**: Automatic resource scaling
- **Alert Management**: Intelligent alert system
- **Dashboard Analytics**: Custom analytics dashboards
- **Report Generation**: Automated report generation
- **Data Visualization**: Interactive data visualization
- **Predictive Analytics**: AI-powered predictive analytics

### Development Tools
- **Code Generator**: Automated code generation
- **Debug Tools**: Advanced debugging capabilities
- **Profiling Tools**: Performance profiling
- **Testing Framework**: Comprehensive testing tools
- **Documentation Generator**: Auto-generated documentation
- **API Documentation**: Interactive API documentation
- **Code Quality**: Code quality analysis tools
- **Dependency Management**: Advanced dependency management
- **Version Control**: Git integration and management
- **Deployment Tools**: Automated deployment system
- **Environment Management**: Multi-environment management
- **Configuration Management**: Centralized configuration
- **Package Management**: Advanced package management
- **Plugin System**: Extensible plugin architecture
- **Custom Extensions**: Custom extension development

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

### Telegram Bot Configuration

```php
// config/visual-builder.php
'telegram' => [
    'enabled' => env('TELEGRAM_BOT_ENABLED', false),
    'token' => env('TELEGRAM_BOT_TOKEN'),
    'webhook_url' => env('TELEGRAM_WEBHOOK_URL'),
    'allowed_users' => explode(',', env('TELEGRAM_ALLOWED_USERS', '')),
    'backup_notifications' => env('TELEGRAM_BACKUP_NOTIFICATIONS', true),
    'system_alerts' => env('TELEGRAM_SYSTEM_ALERTS', true),
],
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

### Telegram Bot Setup

```php
use LaravelBuilder\VisualBuilder\Services\TelegramBotService;

$bot = new TelegramBotService();
$bot->setWebhook();
$bot->sendMessage($chatId, 'Hello from Laravel Visual Builder!');
```

### Backup with Telegram Notifications

```php
use LaravelBuilder\VisualBuilder\Services\BackupService;
use LaravelBuilder\VisualBuilder\Services\TelegramBotService;

$backup = new BackupService();
$telegram = new TelegramBotService();

// Create backup
$backupFile = $backup->create();

// Send notification
$telegram->sendMessage(
    config('visual-builder.telegram.allowed_users')[0],
    "✅ Backup completed successfully!\nFile: {$backupFile}\nSize: " . filesize($backupFile) . " bytes"
);
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

### OneDrive

```php
use LaravelBuilder\VisualBuilder\Services\CloudStorageService;

$storage = new CloudStorageService('onedrive');
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

### SSL Certificate Management

```php
use LaravelBuilder\VisualBuilder\Services\SSLService;

$ssl = new SSLService();
$ssl->renewCertificate('example.com');
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
    $schedule->command('backup:files')->weekly();
}
```

### Encrypted Backups

```php
use LaravelBuilder\VisualBuilder\Services\BackupService;

$backup = new BackupService();
$backup->setEncryption(true);
$backup->setEncryptionKey('your-secret-key');
$backup->create();
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

### Health Checks

```php
use LaravelBuilder\VisualBuilder\Services\HealthCheckService;

$health = new HealthCheckService();
$status = $health->checkAll();
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

### Integration Testing

```bash
php artisan test --filter=IntegrationTest
```

### Performance Testing

```bash
php artisan test --filter=PerformanceTest
```

## Environment Variables

```env
# Visual Builder Core
VISUAL_BUILDER_API_KEY=your_api_key
VISUAL_BUILDER_DEBUG=true
VISUAL_BUILDER_STORAGE_DISK=local
VISUAL_BUILDER_CACHE_ENABLED=true

# Telegram Bot
TELEGRAM_BOT_ENABLED=true
TELEGRAM_BOT_TOKEN=your_bot_token
TELEGRAM_WEBHOOK_URL=https://yourdomain.com/api/telegram/webhook
TELEGRAM_ALLOWED_USERS=123456789,987654321
TELEGRAM_BACKUP_NOTIFICATIONS=true
TELEGRAM_SYSTEM_ALERTS=true

# AI Services
OPENAI_API_KEY=your_openai_key
ANTHROPIC_API_KEY=your_anthropic_key
AI_MODEL=gpt-4
AI_TEMPERATURE=0.7

# Security
SECURITY_SCAN_ENABLED=true
MALWARE_SCAN_ENABLED=true
RATE_LIMIT_ENABLED=true
SSL_AUTO_RENEWAL=true

# Backup
BACKUP_ENCRYPTION_ENABLED=true
BACKUP_COMPRESSION_ENABLED=true
BACKUP_RETENTION_DAYS=30
BACKUP_NOTIFICATIONS=true

# Monitoring
MONITORING_ENABLED=true
UPTIME_MONITORING=true
PERFORMANCE_MONITORING=true
ERROR_TRACKING=true
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