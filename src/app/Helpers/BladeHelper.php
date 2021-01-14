<?php

namespace Backpack\BlockCRUD\app\Helpers;

class BladeHelper {

    public static function customblockDirective($args) {
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
                                if($block->html_content) {
                                    $parameters = array_merge($parameters, $block->html_content);
                                }
                                
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
    }

    public static function pageblocksDirective($content) {
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
                                if($block->html_content) {
                                    $parameters = array_merge($parameters, $block->html_content);
                                }
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
    }

    public static function pageblocksEditDirective($content) {
        $code = '<?php
                $content = \Backpack\BlockCRUD\app\Helpers\BlockCRUDHelper::removeDivs(' . $content . ');

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
                                if($block->html_content) {
                                    $parameters = array_merge($parameters, $block->html_content);
                                }
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

                        $replace = \'<div class="blockcrud_element" data-block="\' . $slug . \'">\' . $replace . \'</div>\';

                        $content = str_ireplace("@customblock-replacing(\'" . $slug . $args . ")", $replace, $content);
                    }
                }

                echo $content;
            ?>';

        return $code;
    }

    public static function sortableBlocksDirective($content) {
        $code = '<?php
                $content = \Backpack\BlockCRUD\app\Helpers\BlockCRUDHelper::removeDivs(' . $content . ');
                $content = \Backpack\BlockCRUD\app\Helpers\BlockCRUDHelper::removeBreaks(' . $content . ');
                $block_items = \Backpack\BlockCRUD\app\Models\BlockItem::orderBy("name")->get();

                $list_arr = explode("@", trim($content));

                $html = \'<ul class="blockcrud_sortable">\';

                foreach($list_arr as $item) {
                    $trimmed_item = trim($item);
                    $block_arr = explode("\'", $trimmed_item);

                    if($trimmed_item !== \'\') {
                        $html .= \'<li class="drag form-control">
                                    \' . (isset($block_arr[1]) && $block_items->where("slug", $block_arr[1])->count() > 0 ? $block_items->where("slug", $block_arr[1])->first()->name : $trimmed_item) . \'
                                    <div class="blockcrud_hidden blockcrud_block_slug">\' . $trimmed_item . \'</div>
                                    <div class="blockcrud-delete-icon js-blockcrud-remove-item la la-trash" title="Убрать блок со страницы"></div>
                                   </li>\';
                    }
                }

                $html .= \'</ul>\';

                echo $html;
            ?>';
        
        return $code;
    }
}
