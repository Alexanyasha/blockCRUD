<?php

namespace Backpack\BlockCRUD;

use Backpack\BlockCRUD\app\Models\BlockItem;
use Backpack\BlockCRUD\app\Observers\BlockItemObserver;
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
            $params = explode(',', $args);
            $parameters = '[]';

            $code = null;

            if(isset($params[0])) {
                $block_name = str_replace('(', '', str_replace('\'', '', $params[0]));
                $block_name_pos = strpos($args, $block_name);
                $par_string = substr_replace($args, '', $block_name_pos, strlen($block_name));
                $par_flag = strpos($par_string, '[');

                if($par_flag !== false) {
                    $scope = str_replace(' ', '', str_replace('\'', '', str_replace(',', '', substr($par_string, 0, $par_flag))));
                    $parameters = substr($par_string, $par_flag);

                } else {
                    $scope = addslashes(str_replace(' ', '', str_replace('\'', '', str_replace(',', '', $par_string))));
                }

                $code = '<?php
                    $block_name = "' . $block_name . '";

                    if(isset($block_name) && $block_name != "") {
                        $block = \Backpack\BlockCRUD\app\Models\BlockItem::active()->where("slug", $block_name)->first();

                        if($block) {
                            $echo = $block->content;
                            $parameters = ' . $parameters . ';

                            if($block->type == "model") {
                                try {
                                    $model = new $block->model();
                                    $scope = "' . $scope . '";

                                    if(isset($scope) && $scope != "") {
                                        $argument = null;
    
                                        $arg_start = strpos($scope, "(");
                                        if($arg_start !== false) {
                                            $argument = str_replace("\"", "", substr($scope, ( (int) $arg_start + 1), -1));
                                            $scope = substr($scope, 0, $arg_start);
                                        }
    
                                        try {
                                        
                                            $items = $model::$scope($argument)->get();
                                        
                                        } catch (\Exception $e) {
                                            logger($e->getMessage());
                                            $items = $block->model::all();
                                        }
                                    } else {
                                        $items = $model::all();
                                    }

                                    if($items) {
                                        $parameters["items"] = $items;
                                    }

    
                                    if(isset($model->blockcrud_template)) {
                                        $echo = view($model->blockcrud_template, $parameters)->render();
                                    } else {
                                        $echo = view("blockcrud::blocks.default", $parameters)->render();
                                    }
                                } catch (\Exception $e) {
                                    logger($e->getMessage());
                                }
                            } elseif($block->type == "template") {
                                try {
                                    $parameters = array_merge($parameters, $block->html_content);
                                    $echo = view($block->model_id, $parameters)->render();
                                } catch (\Exception $e) {
                                    logger($e->getMessage());
                                }
                            } elseif($block->type == "html" && \Storage::exists("blockcrud/html/" . $block->id . ".blade.php")) {
                                try {
                                    $echo = view("blockcrud_storage::html." . $block->id, $parameters)->render();
                                } catch (\Exception $e) {
                                    logger($e->getMessage());
                                }
                            }

                            echo $echo;
                        }
                    }

                ?>';
            }

            return $code;
        });

        Blade::directive('pageblocks', function ($content) {           
            $code = '<?php
                $content = ' . $content . ';

                preg_match_all("/@customblock\(\'(?P<slug>[^\']+)(?P<args>.+)\)/i", $content, $matches);

                foreach($matches["slug"] as $key => $slug) {
                    $args = $matches["args"][$key];
                    $parameters = [];

                    $par_flag = strpos($args, "[");

                    if($par_flag !== false) {
                        $scope = str_replace(" ", "", str_replace("\'", "", str_replace(",", "", substr($args, 0, $par_flag))));

                        $param_string = substr($args, $par_flag);
                        $parameters = \Backpack\BlockCRUD\app\Helpers\BlockCRUDHelper::multi_explode($param_string);
                    } else {
                        $scope = str_replace(" ", "", str_replace("\'", "", str_replace(",", "", $args)));
                    }

                    $block = \Backpack\BlockCRUD\app\Models\BlockItem::active()->where(\'slug\', $slug)->first();

                    if($block) {
                        $content = str_ireplace("@customblock(", "@customblock-replacing(", $content);
                        $replace = $block->content;

                        if($block->type == "model") {
                            try {
                            
                                $model = new $block->model();
                                if(isset($scope) && $scope != "") {
                                    $argument = null;
                                    $arg_start = strpos($scope, "(");
                                    if($arg_start !== false) {
                                        $argument = str_replace("\"", "", substr($scope, ( (int) $arg_start + 1), -1));
                                        $scope = substr($scope, 0, $arg_start);
                                    }
    
                                    try {
                                    
                                        $items = $model::$scope($argument)->get();
                                    
                                    } catch (\Exception $e) {
                                        logger($e->getMessage());
                                        $items = $block->model::all();
                                    }
                                } else {
                                    $items = $block->model::all();
                                }
    
                                if($items) {
                                    $parameters["items"] = $items;
                                }
    
                                if(isset($model->blockcrud_template)) {
                                    $replace = view($model->blockcrud_template, $parameters)->render();
                                } else {
                                    $replace = view("blockcrud::blocks.default", $parameters)->render();
                                }
                            
                            } catch (\Exception $e) {
                                logger($e->getMessage());
                            }

                        } elseif($block->type == "template") {
                            try {
                                $parameters = array_merge($parameters, $block->html_content);
                                $replace = view($block->model_id, $parameters)->render();
                            } catch (\Exception $e) {
                                logger($e->getMessage());
                            }
                        } elseif($block->type == "html" && \Storage::exists("blockcrud/html/" . $block->id . ".blade.php")) {
                            try {
                                $replace = view("blockcrud_storage::html." . $block->id, $parameters)->render();
                            } catch (\Exception $e) {
                                logger($e->getMessage());
                            }
                        }

                        $content = str_ireplace("@customblock-replacing(\'" . $slug . $args . ")", $replace, $content);
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
