<?php
namespace Power\Controllers;
use Power\Models\Extensions;
use Power\Models\Roles;
use Power\Models\Rules;

class RolesController extends AdminBaseController {
    protected $title = '角色管理';
    
    function displayAction(){ $this->indexAction(); }
    function newAction(){ $a = $this->dispatcher->setParam('is_new', true);$this->indexAction(); }
    function indexAction(){
        $rules = $menus = [];
        Rules::getChildIds($rules);
        Rules::expandMenu($rules, $menus);
        $is_new = $this->getParam('is_new');
        $this->view->roles = Roles::find();
        $this->view->is_new = $is_new;
        if($is_new){
            $this->view->role_id = 0;
            $this->view->rules   = [];
            $this->view->extends = [];
        }else{
            if($this->item_id){
                $current_rule = array_column($this->view->roles->toArray(), null, 'role_id')[$this->item_id];
                $this->view->role_id = $this->item_id;
                $this->view->rules   = $current_rule['rules'];
                $this->view->extends = $current_rule['extensions'] ?: '[]';
            }else{
                $this->view->role_id = $this->view->roles[0]->role_id;
                $this->view->rules   = $this->view->roles[0]->rules;
                $this->view->extends = $this->view->roles[0]->extensions ?: '[]';
            }
            $this->view->rules   = json_decode($this->view->rules,1);
            $this->view->extends = json_decode($this->view->extends,1);
        }
        $rule_extends = Extensions::getExtensions('rule');
        if($rule_extends) $global_extends[0] = $rule_extends[0];
        else $global_extends[0] = [];
        unset($rule_extends[0]);
        $this->view->menus = $menus;
        $this->view->rule_extends_html = \AdminHelper::getExtensionsHtml($rule_extends, $this->view->extends, [
            '<span class="btn btn-info btn-flat btn-xs" onclick="toggleExt(event)">扩展权限<i class="fa fa-caret-up"></i></span><div class="ext">',
            '</div>'
        ]);
        $this->view->global_extends_html = \AdminHelper::getExtensionsHtml($global_extends, $this->view->extends);
        $this->render();
    }
    
    function updateAction(){
        $rules = [];
        if(isset($_POST['rule_id'])) foreach($_POST['rule_id'] as $rule_id => $values){
            $value = 0;
            foreach(array_keys($values) as $index){
                $value |= 1 << $index;
            }
            $rules[$rule_id] = $value;
        }
        $rules  = json_encode($rules);
        $extend = isset($_POST['extend']) ? json_encode($_POST['extend'],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null;
        if($this->item_id){
            $role  = Roles::findFirst($this->item_id);
            $role->rules = $rules;
            $role->extensions = $extend;
            $role->save();
        }else{
            $role = new Roles();
            $role->create(
                [
                    'name'  => $_POST['role_name'],
                    'rules' => $rules,
                    'extensions' => $extend
                ]
            );
        }
        $this->item_id = $role->role_id;
        $this->indexAction();
    }
    
    function deleteAction(){
        $this->modelsManager->executeQuery('UPDATE \Power\Models\Users SET role_id = null where role_id=?0',[$this->item_id]);
        Roles::findFirst($this->item_id)->delete();
        $this->jsonOut(['code'=>'ok']);
    }
}
