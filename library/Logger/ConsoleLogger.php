<?php
namespace Logger;

use Colors\Color;
/**
 * 日志分为如下几个字段：
 * 'ip'     => 来源的IP
 * 'from'   => 日志来源,分类
 * 'series' => 序号
 * 'time'   => 产生时间
 * 'title'  => 标题
 * 'body'   => 内容
 */
class ConsoleLogger extends LoggerBaseAbs{
    
    public function console($type, $data){
        $color = new Color();
        $data  = (is_array($data) or is_object($data)) ? print_r($data,1) : $data;
        switch($type){
            case 'debug':
                $type = $color($type.':')->default . '';
                $data = $color($data)->default . '';
                break;
            case 'info':
                $type = $color($type.':')->bold.'';
                $data = $color($data)->default.'';
                break;
            case 'notice':
                $type = $color($type.':')->bold->yellow.'';
                $data = $color($data)->default.'';
                break;
            case 'warning':
                $type = $color($type.':')->bold->red.'';
                $data = $color($data)->red.'';
                break;
            case 'critical':
                $type = $color($type.':')->bold->red->bg_white.'';
                $data = $color($data)->bold->red->bg_white.'';
                break;
            case 'alert':
                $type = $color($type.':')->bold->red->bg_white->underline.'';
                $data = $color($data)->bold->red->bg_white->underline.'';
                break;
            case 'error':
                $type = $color($type.':')->bold->red->bg_light_yellow.'';
                $data = $color($data)->bold->red->bg_light_yellow.'';
                break;
            case 'strict':
                $type = $color($type.':')->bold->red->bg_light_yellow->underline.'';
                $data = $color($data)->bold->red->bg_light_yellow->underline.'';
                break;
        }
        echo $type.$data."\n";
    }
    
    public function debug($data)       { return $this->console("debug",$data);    }
    public function info($data)        { return $this->console("info",$data);     }
    public function notice($data)      { return $this->console("notice",$data);   }
    public function warning($data)     { return $this->console("warning",$data);  }
    public function critical($data)    { return $this->console("critical",$data); }
    public function alert($data)       { return $this->console("alert",$data);    }
    public function error($data)       { return $this->console("error",$data);    }
    public function strict($data)      { return $this->console("strict",$data);   }
}
