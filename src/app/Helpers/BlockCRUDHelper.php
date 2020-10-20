<?php

namespace Backpack\BlockCRUD\app\Helpers;

class BlockCRUDHelper {
    public static function multi_explode($string) {
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

    public static function replaceScripts($string) {
        $tags = ['<script>', '<script ', '</script>'];

        $newtags = ['<replaced-script>', '<replaced-script ', '</replaced-script>'];

        return str_replace($tags, $newtags, $string);
    }
}
