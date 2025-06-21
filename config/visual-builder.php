<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Builder Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure the visual builder settings.
    |
    */

    'frontend' => [
        'framework' => env('VISUAL_BUILDER_FRONTEND', 'livewire'), // livewire or vue
        'theme' => env('VISUAL_BUILDER_THEME', 'light'), // light, dark, or custom
    ],

    'ai' => [
        'enabled' => env('VISUAL_BUILDER_AI_ENABLED', true),
        'provider' => env('VISUAL_BUILDER_AI_PROVIDER', 'openai'),
        'model' => env('VISUAL_BUILDER_AI_MODEL', 'gpt-4'),
    ],

    'api' => [
        'default_version' => env('VISUAL_BUILDER_API_VERSION', 'v1'),
        'auth' => env('VISUAL_BUILDER_API_AUTH', 'sanctum'), // sanctum, passport, or none
        'rate_limit' => env('VISUAL_BUILDER_API_RATE_LIMIT', 60),
        'rate_limit_window' => env('VISUAL_BUILDER_API_RATE_LIMIT_WINDOW', 60),
    ],

    'auth' => [
        'multi_guard' => env('VISUAL_BUILDER_MULTI_GUARD', true),
        'default_guard' => env('VISUAL_BUILDER_DEFAULT_GUARD', 'web'),
        'guards' => [
            'web' => [
                'driver' => 'session',
                'provider' => 'users',
            ],
            'api' => [
                'driver' => 'sanctum',
                'provider' => 'users',
            ],
        ],
    ],

    'permissions' => [
        'enabled' => env('VISUAL_BUILDER_PERMISSIONS_ENABLED', true),
        'provider' => env('VISUAL_BUILDER_PERMISSIONS_PROVIDER', 'spatie'),
    ],

    'components' => [
        'default_layout' => env('VISUAL_BUILDER_DEFAULT_LAYOUT', 'app'),
        'available_components' => [
            'pages',
            'menus',
            'routes',
            'controllers',
            'models',
            'migrations',
            'seeders',
            'components',
            'sidebars',
            'navigation',
            'footers',
        ],
    ],

    'export' => [
        'postman' => [
            'enabled' => env('VISUAL_BUILDER_EXPORT_POSTMAN', true),
            'auto_generate' => env('VISUAL_BUILDER_EXPORT_POSTMAN_AUTO', true),
        ],
        'openapi' => [
            'enabled' => env('VISUAL_BUILDER_EXPORT_OPENAPI', true),
            'auto_generate' => env('VISUAL_BUILDER_EXPORT_OPENAPI_AUTO', true),
        ],
    ],
]; 