<?php

namespace App\Providers;

use App\Builders\PageBuilder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class PageBuilderServiceProvider extends BaseBuilderServiceProvider
{
    protected $builderName = 'page';
    protected $builderConfig = 'page-builder';
    protected $builderViews = 'page';
    protected $builderRoutes = 'page';
    protected $builderMigrations = 'page';
    protected $builderAssets = 'page';
    protected $builderCommands = [
        \App\Console\Commands\PageBuilderCommand::class,
    ];
    protected $builderPolicies = [
        \App\Models\Page::class => \App\Policies\PagePolicy::class,
    ];
    protected $builderEvents = [
        \App\Events\PageCreated::class => [
            \App\Listeners\PageCreatedListener::class,
        ],
        \App\Events\PageUpdated::class => [
            \App\Listeners\PageUpdatedListener::class,
        ],
        \App\Events\PageDeleted::class => [
            \App\Listeners\PageDeletedListener::class,
        ],
    ];
    protected $builderObservers = [
        \App\Models\Page::class => \App\Observers\PageObserver::class,
    ];
    protected $builderComponents = [
        'page' => \App\View\Components\Page::class,
        'page-list' => \App\View\Components\PageList::class,
        'page-form' => \App\View\Components\PageForm::class,
    ];
    protected $builderMiddleware = [
        'page' => \App\Http\Middleware\PageMiddleware::class,
    ];
    protected $builderBindings = [
        \App\Contracts\PageRepository::class => \App\Repositories\PageRepository::class,
    ];
    protected $builderSingletons = [
        \App\Services\PageService::class => \App\Services\PageService::class,
    ];

    public function register()
    {
        parent::register();

        $this->app->singleton(PageBuilder::class, function ($app) {
            return new PageBuilder();
        });
    }

    public function boot()
    {
        parent::boot();

        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang/page', 'page');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views/page', 'page');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/page.php');
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations/page');

        $this->publishes([
            __DIR__ . '/../../config/page-builder.php' => config_path('page-builder.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../../resources/views/page' => resource_path('views/vendor/page'),
        ], 'views');

        $this->publishes([
            __DIR__ . '/../../resources/lang/page' => resource_path('lang/vendor/page'),
        ], 'translations');

        $this->publishes([
            __DIR__ . '/../../resources/assets/page' => public_path('vendor/page'),
        ], 'assets');
    }

    protected function registerDirectives()
    {
        Blade::directive('page', function ($expression) {
            return "<?php echo app(App\View\Components\Page::class)->render($expression); ?>";
        });
    }
} 