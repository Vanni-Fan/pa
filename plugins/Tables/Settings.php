<?php
namespace plugins\Tables;

class Settings {
    public static function setting(){
//        echo "hhhhhhhhhh";
    }
    public static function __callStatic($a, $b){
        var_dump($a, $b);
    }
}