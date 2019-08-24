<?php
namespace Logger;

/**
 * 日志基础抽象类，仅仅空实现了 iLogger 的所有接口，不做任何事，需要对它进行扩展，以实现具体功能
 * Class LoggerBaseAbs
 */
class LoggerBaseAbs implements iLogger {
    public function debug($data){}     # 00000001 调试
    public function notice($data){}    # 00000100 注意
    public function warning($data){}   # 00001000 预警
    public function critical($data){}  # 00010000 黄色预警
    public function alert($data){}     # 00100000 红色预警
    public function error($data){}     # 01000000 错误
    public function strict($data){}    # 10000000 紧急错误
    public function info($data){}      # 00000010 提示
}