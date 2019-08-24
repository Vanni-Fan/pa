<?php
namespace Logger;

/**
 * 定义了几种日志类型
 */
interface iLogger{
    public function debug($data);     # 00000001 调试
    public function info($data);      # 00000010 提示
    public function notice($data);    # 00000100 注意
    public function warning($data);   # 00001000 预警
    public function critical($data);  # 00010000 黄色预警
    public function alert($data);     # 00100000 红色预警
    public function error($data);     # 01000000 错误
    public function strict($data);    # 10000000 紧急错误
}
