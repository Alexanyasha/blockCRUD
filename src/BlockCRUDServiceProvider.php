<?php

namespace Backpack\BlockCRUD;

//use Backpack\BlockCRUD\app\Models\BlockItem;
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

        $this->publishes([__DIR__ . '/resources/css' => public_path('blockcrud/css')], 'blockcrud');
        $this->publishes([__DIR__ . '/resources/js' => public_path('blockcrud/js')], 'blockcrud');

        Blade::directive('customblock', function ($block_name) {
            $code = '<?php
                if(' . $block_name . ') {
                    $block = \Backpack\BlockCRUD\app\Models\BlockItem::active()->where(\'slug\', ' . $block_name . ')->first();

                    if($block) {
                        echo $block->content;
                    }
                }
            ?>';

            return $code;
        });

        Blade::directive('pageblocks', function ($content) {
            $code = '<?php
                $content = ' . $content . ';

                preg_match_all("/@customblock\(\'(?P<slug>.+)\'\)/i", $content, $matches);

                foreach($matches["slug"] as $slug) {
                    $block = \Backpack\BlockCRUD\app\Models\BlockItem::active()->where(\'slug\', $slug)->first();

                    if($block) {
                        $replace = $block->content;

                        $content = str_ireplace("@customblock(\'" . $slug . "\')", $replace, $content);
                    }
                }

                echo $content;
            ?>';

            return $code;
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
