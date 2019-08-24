<?php
namespace Power\Controllers;

use Power\Models\Plugins;

class PluginsController extends AdminBaseController {
    protected $title = '插件管理';
    public function indexAction(){
        $this->view->plugins = Plugins::find(['offset'=>($this->current_page-1) * $this->page_size,'limit' => $this->page_size]);
        $this->view->page = $this->getPaginatorString(Plugins::count());
        $this->render();
    }
    
    public function newAction(){
        $this->render();
    }
    public function setAction(){
        $event  = $this->getParam('event');
        $plugin = Plugins::findFirst($this->item_id);
        $class  = '\\plugins\\'.$plugin->name.'\\Settings';
        if(class_exists($class) && function_exists("$class::$event")) call_user_func("$class::$event", $this, $plugin);
        switch($event){
            case 'enable':
            case 'disable':
                $plugin->enabled = ['enable'=>1,'disable'=>0][$event];
                $plugin->save();
                break;
            case 'delete':
                $plugin->delete();
                break;
            case 'setting': # 设置页面
            case 'upgrade':
            
        }
        $this->response->redirect($this->url(),true);
    }
}