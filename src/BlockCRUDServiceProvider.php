<?php

namespace Backpack\BlockCRUD;

use Backpack\BlockCRUD\app\Models\BlockItem;
use Backpack\BlockCRUD\app\Observers\BlockItemObserver;
use Backpack\BlockCRUD\app\Helpers\BladeHelper;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use View;

class BlockCRUDServiceProvider extends ServiceProvider
{

    public $routeFilePath = '/routes/backpack/blockcrud.php';

    protected $defer = false;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // publish migrations
        $this->loadMigrationsFrom(realpath(__DIR__ . '/database/migrations'));
        $this->loadViewsFrom(realpath(__DIR__ . '/resources/views'), 'blockcrud');
        View::addNamespace('blockcrud', realpath(__DIR__ . '/resources/views'));
        View::addNamespace('blockcrud_storage', storage_path('app/blockcrud'));

        $this->publishes([__DIR__ . '/resources/css' => public_path('blockcrud/css')], 'blockcrud');
        $this->publishes([__DIR__ . '/resources/js' => public_path('blockcrud/js')], 'blockcrud');

        BlockItem::observe(BlockItemObserver::class);

        Blade::directive('customblock', function ($args) {
            return BladeHelper::customblockDirective($args);
        });

        Blade::directive('pageblocks', function ($content) {           
            return BladeHelper::pageblocksDirective($content);
        });

        Blade::directive('pageblocks_edit', function ($content) {           
            return BladeHelper::pageblocksEditDirective($content);
        });

        Blade::directive('pageblocks_sortable', function ($content) {     
            return BladeHelper::sortableBlocksDirective($content);
        });
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function setupRoutes(Router $router)
    {
        // by default, use the routes file provided in vendor
        $routeFilePathInUse = __DIR__ . $this->routeFilePath;

        // but if there's a file with the same name in routes/backpack, use that one
        if (file_exists(base_path($this->routeFilePath))) {
            $routeFilePathInUse = base_path($this->routeFilePath);
        }

        $this->loadRoutesFrom($routeFilePathInUse);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->setupRoutes($this->app->router);
    }
}
