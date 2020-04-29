<?php
namespace Logger;
class Logger implements iLoggerManager{
    static private $level;
    static private $loggers=[];
    
    const OFF      = 0b00000000;
    const STRICT   = 0b10000000;
    const ERROR    = 0b11000000;
    const ALERT    = 0b11100000;
    const CRITICAL = 0b11110000;
    const WARNING  = 0b11111000;
    const NOTICE   = 0b11111100;
    const INFO     = 0b11111110;
    const DEBUG    = 0b11111111;

    public static function log($level, $data){
        if(empty(self::$loggers)) self::$loggers['default'] = new DefaultLogger;
        if(is_null(self::$level)) self::$level = self::ERROR;
        
        # 条件判断，是否存日志 
        $lv = constant('\\'.__CLASS__.'::'.strtoupper($level));
        # error vs debug, 0b01000000 vs 0b00000001
        if(($lv & self::$level) === 0) return false;

        return array_map(function($logger)use($level, $data){
            return $logger->$level($data);
        }, self::$loggers);
    }

    public static function logs(...$data){
        self::log('info', $data);
    }

    public static function setLevel($level){
        self::$level = $level;
    }
    
    public static function getLevel(){
        return self::$level;
    }
    
    public static function addLogger(iLogger $logger, string $name = null){
        if(!$name) $name = get_class($logger);
        self::$loggers[$name] = $logger;
    }
    
    public static function delLogger(string $name){
        unset(self::$loggers[$name]);
    }
    
    public static function getLoggers(){
        return self::$loggers;
    }
    
    public static function getLogger($name)
    {
        return self::$loggers[$name];
    }
    
    public static function __callStatic($name, $params){
        return self::log($name, $params[0]);
    }
}
