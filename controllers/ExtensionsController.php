<?php
namespace Power\Controllers;
use Power\Models\Extensions;
use Power\Models\Rules;

class ExtensionsController extends AdminBaseController {
//    protected $title = '扩展权限';
    protected $type;
    public function initialize()
    {
        parent::initialize();
        $params = $this->getParam();
        $this->type  = $params['type'];
        if($params['type'] == 'rule'){
            $this->title = '扩展权限';
            $this->view->type_name = '扩展';
        }else{
            $this->title = '扩展属性';
            $this->view->type_name = '属性';
        }
    }
    
    public function indexAction(){
        $extends = Extensions::find(['type=?0','bind'=>[$this->type],'order'=>'rule_id'])->toArray();
        $global  = $rules = [];
        foreach($extends as $extend){
            if($extend['rule_id']){
                $rule_info = [];
                Rules::getParentIds($extend['rule_id'], $rule_info);
                $extend['rule_names'] = implode('>', array_map(function($v){ return $v['name']; }, $rule_info));
                $rules[]  = $extend;
            }else{
                $global[] = $extend;
            }
        }
        $this->view->rules  = $rules;
        $this->view->global = $global;
        $this->render();
    }
    
    # 展示
    public function newAction(){ $this->displayAction(); }
    public function displayAction(){
        $rules = $menus = [];
        Rules::getChildIds($rules);
        Rules::expandMenu($rules, $menus);
        $this->view->menus = $menus;
        if($this->item_id){
            $this->view->data = Extensions::findFirst($this->item_id)->toArray();
        }
        $this->render('Extensions/Edit');
    }
    
    # 修改
    public function appendAction(){ $this->updateAction(); }
    public function updateAction(){
        $_POST['type'] = $this->type;
        $_POST['is_action_name'] = $this->request->getPost('is_action_name','int',0);
        if($this->item_id){
            $extend = Extensions::findFirst($this->item_id);
            $extend->update($_POST);
        }else{
            $extend = new Extensions();
            $extend->create($_POST);
        }
        $this->response->redirect($this->url(),true);
    }
    
    # 删除
    public function deleteAction(){
        Extensions::findFirst($this->item_id)->delete();
        $this->jsonOut(['code'=>'ok']);
    }
}
