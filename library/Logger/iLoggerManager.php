<?php
namespace Logger;
/**
 * 管理等级
 */
interface iLoggerManager{
    public static function log($level, $data); # 日志分发
    public static function setLevel($level);   # 设置级别
    public static function addLogger(iLogger $logger, string $name=null);
    public static function getLoggers();
    public static function getLogger($index);
}


