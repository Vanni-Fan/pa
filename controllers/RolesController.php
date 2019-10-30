<?php
namespace Power\Controllers;
use HtmlBuilder\Forms;
use HtmlBuilder\Parser\AdminLte\Parser;
use Power\Models\Configs;
use Power\Models\Roles;
use Power\Models\menus;

class RolesController extends AdminBaseController {
    protected $title = '角色管理';
    
    function displayAction(){ $this->indexAction(); }
    function newAction(){ $a = $this->dispatcher->setParam('is_new', true);$this->indexAction(); }
    function indexAction(){
        $parser = new Parser();
        $this->view->test_div = $parser->parse(
            Forms::input('aaa','测试', 'hhhhh')
        );
        $parser->setResources($this);

        $menus = Menus::getFlatMenus(); // 全部的菜单
        $role_menus = []; // 角色的权限 TODO
        $is_new = $this->getParam('is_new');
        $this->view->roles = Roles::find();
        $this->view->is_new = $is_new;
        if($is_new){
            $this->view->role_id = 0;
            $this->view->menus   = [];
            $this->view->extends = [];
        }else{
            if($this->item_id){
                $current_rule = array_column($this->view->roles->toArray(), null, 'role_id')[$this->item_id];
                $this->view->role_id = $this->item_id;
                $this->view->menus   = $current_rule['menus'];
                $this->view->extends = $current_rule['configs'] ?: '[]';
            }else{
                $this->view->role_id = $this->view->roles[0]->role_id;
//                $this->view->menus   = $this->view->roles[0]->Permissions[0]->Menu;
                $this->view->menus   = '[]';
//                $this->view->extends = $this->view->roles[0]->configs ?: '[]';
                $this->view->extends = '[]';
            }
            $this->view->menus   = json_decode($this->view->menus,1);
            $this->view->extends = json_decode($this->view->extends,1);
        }
        $rule_extends = Configs::getConfigs('rule',true);
        if($rule_extends) $global_extends[0] = $rule_extends[0];
        else $global_extends[0] = [];
        unset($rule_extends[0]);
        $this->view->menus = $menus;
//        $this->view->rule_extends_html = \AdminHelper::getConfigsHtml($rule_extends[$this->getMenuId()], $this->view->extends, $this);
        $this->view->global_extends_html = \AdminHelper::getConfigsHtml($global_extends, $this->view->extends, $this);
        $this->render();
    }
    
    function updateAction(){
        $menus = [];
        if(isset($_POST['menu_id'])) foreach($_POST['menu_id'] as $menu_id => $values){
            $value = 0;
            foreach(array_keys($values) as $index){
                $value |= 1 << $index;
            }
            $menus[$menu_id] = $value;
        }
        $menus  = json_encode($menus);
        $extend = isset($_POST['extend']) ? json_encode($_POST['extend'],JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null;
        if($this->item_id){
            $role  = Roles::findFirst($this->item_id);
            $role->menus = $menus;
            $role->configs = $extend;
            $role->save();
        }else{
            $role = new Roles();
            $role->create(
                [
                    'name'  => $_POST['role_name'],
                    'menus' => $menus,
                    'configs' => $extend
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
