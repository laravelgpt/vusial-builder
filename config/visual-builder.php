<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Visual Builder Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the Laravel Visual Builder
    | package. You can customize these settings according to your needs.
    |
    */

    'telegram' => [
        'enabled' => env('TELEGRAM_BOT_ENABLED', false),
        'token' => env('TELEGRAM_BOT_TOKEN', ''),
        'webhook_url' => env('TELEGRAM_WEBHOOK_URL', ''),
        'allowed_users' => explode(',', env('TELEGRAM_ALLOWED_USERS', '')),
        'backup_notifications' => env('TELEGRAM_BACKUP_NOTIFICATIONS', true),
        'system_alerts' => env('TELEGRAM_SYSTEM_ALERTS', true),
    ],

    'backup' => [
        'enabled' => env('BACKUP_ENABLED', true),
        'backup_path' => env('BACKUP_PATH', storage_path('backups')),
        'exclude_paths' => [
            'node_modules',
            'vendor',
            '.git',
            'storage/logs',
            'storage/framework/cache',
            'storage/framework/sessions',
            'storage/framework/views'
        ],
        'max_backups' => env('BACKUP_MAX_COUNT', 10),
        'compress_backups' => env('BACKUP_COMPRESS', true),
        'notify_on_completion' => env('BACKUP_NOTIFY', true),
        'schedule' => [
            'enabled' => env('BACKUP_SCHEDULE_ENABLED', true),
            'frequency' => env('BACKUP_SCHEDULE_FREQUENCY', 'daily'), // daily, weekly, monthly
            'time' => env('BACKUP_SCHEDULE_TIME', '02:00'),
        ],
    ],

    'cloud_storage' => [
        'enabled' => env('CLOUD_STORAGE_ENABLED', false),
        'driver' => env('CLOUD_STORAGE_DRIVER', 'local'), // local, s3, gcs, azure, dropbox
        'path' => env('CLOUD_STORAGE_PATH', storage_path('app/public')),
        
        // AWS S3 Configuration
        's3' => [
            'bucket' => env('AWS_BUCKET', ''),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'key' => env('AWS_ACCESS_KEY_ID', ''),
            'secret' => env('AWS_SECRET_ACCESS_KEY', ''),
        ],

        // Google Cloud Storage Configuration
        'gcs' => [
            'bucket' => env('GCS_BUCKET', ''),
            'project_id' => env('GCS_PROJECT_ID', ''),
            'key_file' => env('GCS_KEY_FILE', ''),
        ],

        // Azure Blob Storage Configuration
        'azure' => [
            'container' => env('AZURE_CONTAINER', ''),
            'account' => env('AZURE_ACCOUNT', ''),
            'key' => env('AZURE_KEY', ''),
        ],

        // Dropbox Configuration
        'dropbox' => [
            'access_token' => env('DROPBOX_ACCESS_TOKEN', ''),
            'app_key' => env('DROPBOX_APP_KEY', ''),
            'app_secret' => env('DROPBOX_APP_SECRET', ''),
        ],
    ],

    'security' => [
        'enabled' => env('SECURITY_MONITORING_ENABLED', true),
        'malware_scanning' => env('SECURITY_MALWARE_SCANNING', true),
        'vulnerability_scanning' => env('SECURITY_VULNERABILITY_SCANNING', true),
        'file_integrity_monitoring' => env('SECURITY_FILE_INTEGRITY', true),
        'log_monitoring' => env('SECURITY_LOG_MONITORING', true),
        'rate_limiting' => env('SECURITY_RATE_LIMITING', true),
        'notify_on_threats' => env('SECURITY_NOTIFY_THREATS', true),
        'scan_schedule' => [
            'enabled' => env('SECURITY_SCAN_SCHEDULE_ENABLED', true),
            'frequency' => env('SECURITY_SCAN_FREQUENCY', 'daily'), // daily, weekly
            'time' => env('SECURITY_SCAN_TIME', '03:00'),
        ],
    ],

    'monitoring' => [
        'enabled' => env('SYSTEM_MONITORING_ENABLED', true),
        'system_monitoring' => env('MONITORING_SYSTEM', true),
        'performance_monitoring' => env('MONITORING_PERFORMANCE', true),
        'error_monitoring' => env('MONITORING_ERRORS', true),
        'uptime_monitoring' => env('MONITORING_UPTIME', true),
        'database_monitoring' => env('MONITORING_DATABASE', true),
        'notify_on_issues' => env('MONITORING_NOTIFY_ISSUES', true),
        'alert_thresholds' => [
            'cpu_usage' => env('MONITORING_CPU_THRESHOLD', 80),
            'memory_usage' => env('MONITORING_MEMORY_THRESHOLD', 85),
            'disk_usage' => env('MONITORING_DISK_THRESHOLD', 90),
            'error_rate' => env('MONITORING_ERROR_THRESHOLD', 5),
            'response_time' => env('MONITORING_RESPONSE_THRESHOLD', 2000),
        ],
        'metrics_collection' => [
            'enabled' => env('MONITORING_METRICS_ENABLED', true),
            'interval' => env('MONITORING_METRICS_INTERVAL', 300), // 5 minutes
            'retention_days' => env('MONITORING_METRICS_RETENTION', 30),
        ],
    ],

    'ai' => [
        'enabled' => env('AI_FEATURES_ENABLED', false),
        'api_key' => env('OPENAI_API_KEY', ''),
        'model' => env('OPENAI_MODEL', 'gpt-4'),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 2000),
        'temperature' => env('OPENAI_TEMPERATURE', 0.7),
    ],

    'ui' => [
        'theme' => env('VISUAL_BUILDER_THEME', 'default'),
        'dark_mode' => env('VISUAL_BUILDER_DARK_MODE', false),
        'sidebar_collapsed' => env('VISUAL_BUILDER_SIDEBAR_COLLAPSED', false),
        'auto_save' => env('VISUAL_BUILDER_AUTO_SAVE', true),
        'auto_save_interval' => env('VISUAL_BUILDER_AUTO_SAVE_INTERVAL', 30000), // 30 seconds
    ],

    'features' => [
        'page_builder' => env('FEATURE_PAGE_BUILDER', true),
        'component_builder' => env('FEATURE_COMPONENT_BUILDER', true),
        'api_builder' => env('FEATURE_API_BUILDER', true),
        'auth_builder' => env('FEATURE_AUTH_BUILDER', true),
        'ai_agent_builder' => env('FEATURE_AI_AGENT_BUILDER', true),
        'chart_builder' => env('FEATURE_CHART_BUILDER', true),
        'dashboard_builder' => env('FEATURE_DASHBOARD_BUILDER', true),
        'form_builder' => env('FEATURE_FORM_BUILDER', true),
        'table_builder' => env('FEATURE_TABLE_BUILDER', true),
        'report_builder' => env('FEATURE_REPORT_BUILDER', true),
        'export_builder' => env('FEATURE_EXPORT_BUILDER', true),
        'import_builder' => env('FEATURE_IMPORT_BUILDER', true),
        'filter_builder' => env('FEATURE_FILTER_BUILDER', true),
        'menu_builder' => env('FEATURE_MENU_BUILDER', true),
        'notification_builder' => env('FEATURE_NOTIFICATION_BUILDER', true),
        'mail_builder' => env('FEATURE_MAIL_BUILDER', true),
        'job_builder' => env('FEATURE_JOB_BUILDER', true),
        'event_builder' => env('FEATURE_EVENT_BUILDER', true),
        'command_builder' => env('FEATURE_COMMAND_BUILDER', true),
        'migration_builder' => env('FEATURE_MIGRATION_BUILDER', true),
        'seeder_builder' => env('FEATURE_SEEDER_BUILDER', true),
        'policy_builder' => env('FEATURE_POLICY_BUILDER', true),
        'resource_builder' => env('FEATURE_RESOURCE_BUILDER', true),
        'route_builder' => env('FEATURE_ROUTE_BUILDER', true),
        'view_builder' => env('FEATURE_VIEW_BUILDER', true),
        'model_builder' => env('FEATURE_MODEL_BUILDER', true),
        'controller_builder' => env('FEATURE_CONTROLLER_BUILDER', true),
        'service_builder' => env('FEATURE_SERVICE_BUILDER', true),
        'repository_builder' => env('FEATURE_REPOSITORY_BUILDER', true),
        'observer_builder' => env('FEATURE_OBSERVER_BUILDER', true),
        'request_builder' => env('FEATURE_REQUEST_BUILDER', true),
        'config_builder' => env('FEATURE_CONFIG_BUILDER', true),
        'middleware_builder' => env('FEATURE_MIDDLEWARE_BUILDER', true),
        'database_backup_builder' => env('FEATURE_DATABASE_BACKUP_BUILDER', true),
        'information_builder' => env('FEATURE_INFORMATION_BUILDER', true),
        'security_builder' => env('FEATURE_SECURITY_BUILDER', true),
    ],

    'permissions' => [
        'admin' => [
            'can_access_all_features' => true,
            'can_manage_users' => true,
            'can_manage_settings' => true,
            'can_view_logs' => true,
            'can_perform_backups' => true,
            'can_manage_security' => true,
        ],
        'developer' => [
            'can_access_all_features' => true,
            'can_manage_users' => false,
            'can_manage_settings' => false,
            'can_view_logs' => true,
            'can_perform_backups' => true,
            'can_manage_security' => false,
        ],
        'user' => [
            'can_access_all_features' => false,
            'can_manage_users' => false,
            'can_manage_settings' => false,
            'can_view_logs' => false,
            'can_perform_backups' => false,
            'can_manage_security' => false,
        ],
    ],

    'cache' => [
        'enabled' => env('VISUAL_BUILDER_CACHE_ENABLED', true),
        'ttl' => env('VISUAL_BUILDER_CACHE_TTL', 3600), // 1 hour
        'prefix' => env('VISUAL_BUILDER_CACHE_PREFIX', 'vb_'),
    ],

    'logging' => [
        'enabled' => env('VISUAL_BUILDER_LOGGING_ENABLED', true),
        'level' => env('VISUAL_BUILDER_LOG_LEVEL', 'info'),
        'channel' => env('VISUAL_BUILDER_LOG_CHANNEL', 'stack'),
    ],

    'rate_limiting' => [
        'enabled' => env('VISUAL_BUILDER_RATE_LIMITING_ENABLED', true),
        'max_requests' => env('VISUAL_BUILDER_RATE_LIMIT_MAX', 100),
        'decay_minutes' => env('VISUAL_BUILDER_RATE_LIMIT_DECAY', 1),
    ],
]; 