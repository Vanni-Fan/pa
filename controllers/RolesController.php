<?php
namespace Power\Controllers;
use Power\Models\Configs;
use Power\Models\Permissions;
use Power\Models\Roles;
use Power\Models\menus;

class RolesController extends AdminBaseController {
    protected $title = '角色管理';
    
    function displayAction(){ $this->indexAction(); }
    function newAction(){ $a = $this->dispatcher->setParam('is_new', true);$this->indexAction(); }
    function indexAction(){
        $menus = Menus::getFlatMenus(); // 全部的菜单
        $is_new = $this->getParam('is_new');

        // 全部的角色
        $this->view->roles = Roles::find();
        $this->view->is_new = $is_new;

        if($is_new){
            $this->view->role_id = 0;
            $this->view->permissions = [];
        }else{
            if($this->item_id){
                $current_rule = array_column($this->view->roles->toArray(), null, 'role_id')[$this->item_id];
                $this->view->role_id = $this->item_id;
            }else{
                $this->view->role_id = $this->view->roles[0]->role_id;
            }
            $this->view->permissions = Roles::getPermissions($this->view->role_id);
        }
        $this->view->menus = $menus;

        $rule_extends = Configs::getConfigs('rule');
//        echo "所有权限配置：",print_r($rule_extends,1);
//        echo "配置了的权限：",print_r($this->view->permissions,1);
//        exit;

        // view->configs 扩展权限
        $this->view->configs = \AdminHelper::getConfigsHtmlGroup($rule_extends, $this->view->permissions['config']??[], $this);
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
        $extend = $_POST['menu_configs'] ?? [];

        // 不存在则创建，然后先删除权限，再创建权限
        if(!$this->item_id){
            $role = new Roles;
            $role->assign(['name'=>$_POST['role_name'],'is_enabled'=>1])->create();
            $this->item_id = $role->role_id;
        }else{
            Permissions::find(['role_id=?0','bind'=>[$this->item_id]])->delete(); // 删除所有权限
        }

        // 添加菜单权限
        foreach($menus as $menu_id=>$menu_value) {
            (new Permissions)->assign(
                [
                    'role_id'   => $this->item_id,
                    'type'      => 'menu',
                    'menu_id'   => $menu_id ?: null,
                    'config_id' => null,
                    'value'     => $menu_value
                ]
            )->create();
        }
        // 添加附件权限
        foreach($extend as $menu_id=>$configs){
            foreach($configs as $var_name=>$var_value){
                $config = Configs::getConfig('rule', $menu_id, $var_name);
                (new Permissions)->assign(
                    [
                        'role_id'   => $this->item_id,
                        'type'      => 'config',
                        'menu_id'   => $menu_id ?: null,
                        'config_id' => $config['config_id'] ?: null,
                        'value'     => $config['var_type'] === 'text' ? $var_value : json_encode($var_value)
                    ]
                )->create();
            }
        }
        $this->indexAction();
    }
    
    function deleteAction(){
        $this->modelsManager->executeQuery('UPDATE \Power\Models\Users SET role_id = null where role_id=?0',[$this->item_id]);
        Roles::findFirst($this->item_id)->delete();
        $this->jsonOut(['code'=>'ok']);
    }
}
