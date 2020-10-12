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

        Blade::directive('customblock', function ($args) {
            $params = explode(',', $args);
            $parameters = '[]';

            $code = null;

            if(isset($params[0])) {
                $block_name = str_replace('(', '', str_replace('\'', '', $params[0]));
                $par_string = str_replace($block_name, '', $args);
                $par_flag = strpos($par_string, '[');

                if($par_flag !== false) {
                    $scope = str_replace(' ', '', str_replace('\'', '', str_replace(',', '', substr($par_string, 0, $par_flag))));
                    $parameters = substr($par_string, $par_flag);

                } else {
                    $scope = str_replace(' ', '', str_replace('\'', '', str_replace(',', '', $par_string)));
                }

                $code = '<?php
                    if(' . isset($block_name) . ') {
                        $block = \Backpack\BlockCRUD\app\Models\BlockItem::active()->where("slug", "' . $block_name . '")->first();

                        if($block) {
                            $echo = $block->content;
                            $parameters = ' . $parameters . ';

                            if($block->type == "model") {
                                if(' . isset($scope) . ' && "' . $scope . '" != "") {
                                    try {
                                    
                                        $items = $block->model::{"' . $scope . '"}()->get();
                                    
                                    } catch (\Exception $e) {
                                        logger($e->getMessage());
                                        $items = $block->model::all();
                                    }
                                } else {
                                    $items = $block->model::all();
                                }

                                if($items) {
                                    $parameters["items"] = $items;

                                    if(isset($items->first()->blockcrud_template)) {
                                        $echo = view($items->first()->blockcrud_template, $parameters)->render();
                                    } else {
                                        $echo = view("blockcrud::blocks.default", $parameters)->render();
                                    }
                                }
                            } elseif($block->type == "template") {
                                try {
                                    $echo = view($block->model_id, $parameters)->render();
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

                function multi_explode($string) {
                    $string = substr(substr($string, 0, -1), 1, strlen($string) - 1);
                    $out = [];

                    $string = preg_replace_callback("/\[.*?\=\>.*?\]/i", function($match) {
                        if(isset($match[0])) {
                            $replaced = preg_replace("/\=\>/i", "|>", $match[0]);
                            $replaced = preg_replace("/\,/i", "|", $replaced);
                        
                            return $replaced;
                        }

                    }, $string);

                    $arr = explode(",", $string);

                    foreach($arr as $line) {
                        $line = trim($line);

                        if(strpos($line, "[") === false) {
                            if(strpos($line, "=>") === false) {
                                $out[] = str_replace("\'", "", $line);
                            } else {
                                $mini_array = explode("=>", $line);

                                if(isset($mini_array[0]) && isset($mini_array[1])) {
                                    $out[trim(str_replace("\'", "", $mini_array[0]))] = trim(str_replace("\'", "", $mini_array[1]));
                                }
                            }
                        } else {
                            if(strpos($line, "=>") !== false) {
                                $mini_array = explode("=>", $line);

                                if(isset($mini_array[0]) && isset($mini_array[1])) {
                                
                                    $nested_array = preg_replace_callback("/\[.*?\|\>.*?\]/i", function($match) {
                                        
                                        if(isset($match[0])) {
                                            $replaced = preg_replace("/\|\>/i", "=>", $match[0]);
                                            $replaced = preg_replace("/\|/i", ",", $replaced);
                                        
                                            return $replaced;
                                        }
                
                                    }, $mini_array[1]);

                                    $out[trim(str_replace("\'", "", $mini_array[0]))] = multi_explode(trim($nested_array));
                                }

                            }
                        }
                    }

                    return $out;
                }

                foreach($matches["slug"] as $key => $slug) {
                    $args = $matches["args"][$key];
                    $parameters = [];

                    $par_flag = strpos($args, "[");

                    if($par_flag !== false) {
                        $scope = str_replace(" ", "", str_replace("\'", "", str_replace(",", "", substr($args, 0, $par_flag))));

                        $param_string = substr($args, $par_flag);
                        $parameters = multi_explode($param_string);
                    } else {
                        $scope = str_replace(" ", "", str_replace("\'", "", str_replace(",", "", $args)));
                    }

                    $block = \Backpack\BlockCRUD\app\Models\BlockItem::active()->where(\'slug\', $slug)->first();

                    if($block) {
                        $content = str_ireplace("@customblock(", "@customblock-replacing(", $content);
                        $replace = $block->content;

                        if($block->type == "model") {
                            $model = new $block->model;
                            if(isset($scope) && $scope != "") {
                                try {
                                
                                    $items = $model::{$scope}()->get();
                                
                                } catch (\Exception $e) {
                                    logger($e->getMessage());
                                    $items = $model::all();
                                }
                            } else {
                                $items = $model::all();
                            }

                            if($items) {
                                $parameters["items"] = $items;
                                if(isset($model->blockcrud_template)) {
                                    $replace = view($model->blockcrud_template, $parameters)->render();
                                } else {
                                    $replace = view("blockcrud::blocks.default", $parameters)->render();
                                }
                            }
                        } elseif($block->type == "template") {
                            try {
                                $replace = view($block->model_id, $parameters)->render();
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
