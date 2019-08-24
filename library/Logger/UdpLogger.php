<?php
namespace Logger;
/**
 * 日志分为如下几个字段：
 * 'ip'     => 来源的IP
 * 'from'   => 日志来源,分类
 * 'series' => 序号
 * 'time'   => 产生时间
 * 'title'  => 标题
 * 'body'   => 内容
 */
class UdpLogger implements iLogger{
    static private $socket;
    static private $series;
    static private $connected;
    static private $from;
    static private $ip;
    static private $port;
    protected $is_cli;

    public function __construct($ip, $port){
        self::$ip = $ip;
        self::$port = $port;
        $this->is_cli = strpos(php_sapi_name(),'cli') !== false;
        if(is_null(self::$socket)) self::connect();
        if(is_null(self::$series)) self::$series = uniqid();
    }
    
    public static function connect(){
        self::$socket = \socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        set_error_handler(function(){});
        $ok = \socket_connect(self::$socket, self::$ip, self::$port);
        if($ok){
            register_shutdown_function(function(){\socket_close(self::$socket);});
            self::$connected = true;
        }
        restore_error_handler();
    }
    
    public static function setSeries($series){
        self::$series = $series;
    }
    
    public static function setFrom($from){
        self::$from = $from;
    }

    public function send($str){
        if(!self::$connected) return false;
        set_error_handler(function(){});
        $ok = \socket_write(self::$socket, $str, strlen($str));
        restore_error_handler();
        return $ok;
    }

    public function write($type, $data){
        $result = [
            "type" => $type,
            "data" => [
                'ip'     => $this->is_cli ? '127.0.0.1' : ($_SERVER['REMOTE_ADDR'] ?? ''),
                'from'   => $data['from'] ?? self::$from ?? 'web',
                'series' => self::$series,
                'time'   => time(),
                'title'  => $data['title'] ?? $_SERVER['REQUEST_URI'] ?? '',
                'body'   => $data['body']  ?? $data
            ]
        ];

        $data = is_string($data) ? $data : json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
        $data_len = strlen($data);
        $one_step = 60*1024;
    
        if($data_len > $one_step){ // 分片发送
            for($i=0,$j=0; $i<$data_len; $i+=$one_step,$j++){
                if($j>=5) break;
                $result['data']['body'] = "PART[$j]：".substr($data, $i, $one_step);
                $this->send(json_encode($result, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
            }
            return true;
        }else{
            return $this->send(json_encode($result, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
        }
    }

    public function debug($data)       { return $this->write("debug",$data);    }
    public function info($data)        { return $this->write("info",$data);     }
    public function notice($data)      { return $this->write("notice",$data);   }
    public function warning($data)     { return $this->write("warning",$data);  }
    public function critical($data)    { return $this->write("critical",$data); }
    public function alert($data)       { return $this->write("alert",$data);    }
    public function error($data)       { return $this->write("error",$data);    }
    public function strict($data)      { return $this->write("strict",$data);   }
}