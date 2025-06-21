<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Page Builder Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration settings for the Page Builder.
    |
    */

    'name' => 'Page Builder',
    'version' => '1.0.0',
    'description' => 'A powerful page builder for Laravel applications',

    /*
    |--------------------------------------------------------------------------
    | Page Model Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the page model settings.
    |
    */

    'model' => [
        'table' => 'pages',
        'fillable' => [
            'title',
            'slug',
            'content',
            'meta_title',
            'meta_description',
            'meta_keywords',
            'status',
            'published_at',
        ],
        'casts' => [
            'published_at' => 'datetime',
            'status' => 'boolean',
        ],
        'dates' => [
            'published_at',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Page Views Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the page views settings.
    |
    */

    'views' => [
        'layout' => 'layouts.app',
        'index' => 'pages.index',
        'show' => 'pages.show',
        'create' => 'pages.create',
        'edit' => 'pages.edit',
    ],

    /*
    |--------------------------------------------------------------------------
    | Page Routes Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the page routes settings.
    |
    */

    'routes' => [
        'prefix' => 'pages',
        'middleware' => ['web', 'auth'],
        'name' => 'pages.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Page Components Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the page components settings.
    |
    */

    'components' => [
        'page' => [
            'class' => \App\View\Components\Page::class,
            'view' => 'components.page',
        ],
        'page-list' => [
            'class' => \App\View\Components\PageList::class,
            'view' => 'components.page-list',
        ],
        'page-form' => [
            'class' => \App\View\Components\PageForm::class,
            'view' => 'components.page-form',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Page Policies Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the page policies settings.
    |
    */

    'policies' => [
        'model' => \App\Models\Page::class,
        'policy' => \App\Policies\PagePolicy::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Page Events Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the page events settings.
    |
    */

    'events' => [
        'created' => \App\Events\PageCreated::class,
        'updated' => \App\Events\PageUpdated::class,
        'deleted' => \App\Events\PageDeleted::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Page Observers Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the page observers settings.
    |
    */

    'observers' => [
        'model' => \App\Models\Page::class,
        'observer' => \App\Observers\PageObserver::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Page Services Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the page services settings.
    |
    */

    'services' => [
        'repository' => \App\Repositories\PageRepository::class,
        'service' => \App\Services\PageService::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Page Middleware Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the page middleware settings.
    |
    */

    'middleware' => [
        'page' => \App\Http\Middleware\PageMiddleware::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Page Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the page cache settings.
    |
    */

    'cache' => [
        'enabled' => true,
        'ttl' => 3600,
        'tag' => 'pages',
    ],

    /*
    |--------------------------------------------------------------------------
    | Page SEO Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the page SEO settings.
    |
    */

    'seo' => [
        'enabled' => true,
        'meta_title' => true,
        'meta_description' => true,
        'meta_keywords' => true,
        'open_graph' => true,
        'twitter_cards' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Page Media Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the page media settings.
    |
    */

    'media' => [
        'enabled' => true,
        'disk' => 'public',
        'path' => 'pages',
        'allowed_types' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/pdf',
        ],
        'max_size' => 5242880, // 5MB
    ],

    /*
    |--------------------------------------------------------------------------
    | Page Editor Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the page editor settings.
    |
    */

    'editor' => [
        'enabled' => true,
        'type' => 'wysiwyg',
        'options' => [
            'height' => 400,
            'toolbar' => [
                'bold',
                'italic',
                'underline',
                'strike',
                'link',
                'image',
                'video',
                'code',
                'clean',
            ],
        ],
    ],
]; 