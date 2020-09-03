<?php

namespace Backpack\BlockCRUD;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

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
        $this->publishes([__DIR__ . '/database/migrations' => database_path('migrations')], 'migrations');
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
