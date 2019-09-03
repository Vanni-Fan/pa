<?php
namespace plugins\Tables;
use PA;
class Settings {
    public static function setting(){
        PA::$dispatch->forward(
            [
                'controller'=>'Manager',
                'namespace'=>'plugins\Tables\Controllers',
                'action'=>'settings',
            ]
        );
        return false;
    }
    public static function __callStatic($a, $b){
        var_dump($a, $b);
    }
}