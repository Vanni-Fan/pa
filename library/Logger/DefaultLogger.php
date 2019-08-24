<?php
namespace Logger;
class DefaultLogger implements iLogger{
    public function debug($data)       { return print_r($data,1);                                  }
    public function info($data)        { return trigger_error(print_r($data,1),E_USER_NOTICE);     }
    public function notice($data)      { return trigger_error(print_r($data,1),E_USER_DEPRECATED); }
    public function warning($data)     { return trigger_error(print_r($data,1),E_USER_WARNING);    }
    public function critical($data)    { return trigger_error(print_r($data,1),E_USER_ERROR);      }
    public function alert($data)       { throw new \Exception(print_r($data,1));                   }
    public function error($data)       { throw new \Error(print_r($data,1));                       }
    public function strict($data)      { throw new \ErrorException(print_r($data,1));              }
}


