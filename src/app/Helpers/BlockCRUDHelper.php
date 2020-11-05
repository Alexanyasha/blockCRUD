<?php

namespace Backpack\BlockCRUD\app\Helpers;

class BlockCRUDHelper {

    public static function prepareArrKey($string) {
        return preg_replace('/\s+/', '', str_replace(' ', '', str_replace("/'", "", str_replace("'", "", $string))));
    }

    public static function multi_explode($string) {
        $string = html_entity_decode(trim($string));
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
                        $arr_key = self::prepareArrKey($mini_array[0]);

                        $out[$arr_key] = trim(str_replace("/'", "", str_replace("'", "", $mini_array[1])));
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

                        $arr_key = self::prepareArrKey($mini_array[0]);

                        $out[$arr_key] = multi_explode(trim($nested_array));
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
