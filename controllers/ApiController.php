<?php
namespace Power\Controllers;
use PA;
use Phalcon\Mvc\Controller;

class ApiController extends Controller{
    
    public function initialize(){
    }
    
    public function afterExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher) {
        $this->view->disable();
        $this->response->setHeader('Access-Control-Allow-Origin','*');
        $this->response->setHeader('Access-Control-Allow-Methods','*');
        $this->response->setHeader('Access-Control-Allow-Headers','*');
        $this->response->setHeader('x-powered-by',PA::$config['site.domain.logogram'].'/'.PA::$config['site.version']);
        $this->response->setContentType('application/json', 'UTF-8');
        if($this->view->error){
            $data = [
                'msg' => $this->view->error,
                'data'=> $this->view->data ?? ''
            ];
        }else{
            $data = [
                'msg' => $this->view->msg  ?? 'ok',
                'data'=> $this->view->data ?? ''
            ];
        }
        return $this->response->setContent(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE))->send();
    }
}
