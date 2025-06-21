<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Package Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration settings for the package.
    |
    */

    'framework' => env('PACKAGE_FRAMEWORK', 'blade'),

    'components' => [
        'accordion' => [
            'enabled' => true,
            'default_icon' => 'chevron-down',
            'animation_duration' => 300,
        ],
        'alert' => [
            'enabled' => true,
            'dismissible' => true,
            'auto_dismiss' => false,
            'dismiss_delay' => 5000,
        ],
        'button' => [
            'enabled' => true,
            'default_variant' => 'primary',
            'default_size' => 'md',
        ],
        'card' => [
            'enabled' => true,
            'default_variant' => 'default',
            'show_header' => true,
            'show_footer' => true,
        ],
        'form' => [
            'enabled' => true,
            'default_layout' => 'vertical',
            'show_labels' => true,
            'show_help_text' => true,
        ],
        'tabs' => [
            'enabled' => true,
            'default_variant' => 'line',
            'animation_duration' => 300,
        ],
        'toast' => [
            'enabled' => true,
            'position' => 'bottom-right',
            'auto_dismiss' => true,
            'dismiss_delay' => 5000,
        ],
    ],

    'styles' => [
        'theme' => env('PACKAGE_THEME', 'light'),
        'primary_color' => env('PACKAGE_PRIMARY_COLOR', '#3B82F6'),
        'secondary_color' => env('PACKAGE_SECONDARY_COLOR', '#6B7280'),
        'success_color' => env('PACKAGE_SUCCESS_COLOR', '#10B981'),
        'danger_color' => env('PACKAGE_DANGER_COLOR', '#EF4444'),
        'warning_color' => env('PACKAGE_WARNING_COLOR', '#F59E0B'),
        'info_color' => env('PACKAGE_INFO_COLOR', '#3B82F6'),
    ],

    'assets' => [
        'load_css' => true,
        'load_js' => true,
        'version' => '1.0.0',
    ],

    'dependencies' => [
        'blade' => [
            'required' => [],
            'optional' => [],
        ],
        'livewire' => [
            'required' => [
                'livewire/livewire',
            ],
            'optional' => [],
        ],
        'vue' => [
            'required' => [
                'vue',
            ],
            'optional' => [
                '@vue/compiler-sfc',
            ],
        ],
        'react' => [
            'required' => [
                'react',
                'react-dom',
            ],
            'optional' => [
                '@babel/preset-react',
            ],
        ],
    ],
]; 