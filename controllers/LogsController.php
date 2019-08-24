<?php
namespace Power\Controllers;

use Power\Models\Logs;

class LogsController extends AdminBaseController {
    protected $title = '日志管理';
    public function indexAction(){
        $this->view->logs = Logs::find(['order'=>'created_time desc','offset'=>($this->current_page-1) * $this->page_size,'limit' => $this->page_size]);
        $this->view->page = $this->getPaginatorString(Logs::count());
        $this->render();
    }
    
}