<?php
namespace Power\Controllers;
use Exceptions\ParamException;
use Power\Models\Roles;
use Power\Models\menus;
use PA;

class MenusController extends AdminBaseController {
    protected $title = '权限管理';
    
    function indexAction($is_new=false){
        $this->view->menus = Menus::getFlatMenus();
        $this->render();
    }
    
    function updateAction(){
        $router = $params = [];
        $router['controller'] = $this->filter->sanitize($_POST['router']['controller'], 'string');
        if(!empty($_POST['router']['action']))    $router['action']    = $this->filter->sanitize($_POST['router']['action'], 'string');
        if(!empty($_POST['router']['module']))    $router['module']    = $this->filter->sanitize($_POST['router']['module'], 'string');
        if(!empty($_POST['router']['path']))      $router['path']      = $this->filter->sanitize($_POST['router']['path'], 'string');
        if(!empty($_POST['router']['namespace'])) $router['namespace'] = $this->filter->sanitize($_POST['router']['namespace'], 'string');
        if(!empty($_POST['router']['priority']))  $router['priority']  = (int)$this->filter->sanitize($_POST['router']['priority'], 'int');
        if(!empty($_POST['params'])) $params = json_decode($_POST['params'],1);
        $is_dir = $_POST['is_dir'] ? true : false;
        $data = [
            'name' => $_POST['name'],
            'icon' => $this->request->getPost('icon','string',''),
            'parent_id' => $this->request->getPost('parent_id','int') ?: 0,
            'index' => $this->request->getPost('index','int') ?: 0,
            'params' => json_encode($params, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'router' => $is_dir ? null : json_encode($router,JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'url_suffix'=>$_POST['url_suffix'] ?: null,
        ];
        if($this->item_id){
            $obj = Menus::findFirst($this->item_id);
            $obj->update($data);
        }else{
            $obj = new menus();
            $obj->create($data);
        }
        $this->response->redirect($this->url('index'),true);
    }
    
    function getIconsAction(){
        $file = POWER_BASE_DIR.'public/dist/bower_components/font-awesome/scss/_icons.scss';
        $fa = '';
        foreach(file($file) as $row){
            if($index = strpos($row,':')){
                $fa .= '<i onclick="selectIcon(this)" class="fa fa-'.substr($row,19,$index-19).'"></i>';
            }
        }
        echo $fa;
    }
    
    function deleteAction(){
        $this->modelsManager->executeQuery('UPDATE \Power\Models\Menus SET parent_id = null where parent_id=?0',[$this->item_id]);
        Menus::findFirst($this->item_id)->delete();
        $this->jsonOut(['code'=>'ok']);
    }
    
    function newAction(){ $this->displayAction(); }
    function displayAction(){
        $this->addCss('/dist/bower_components/select2/dist/css/select2.min.css','before');
        # 所有父级菜单
        $this->view->menus = Menus::getFlatMenus();
    
        # 所有模块
        $modules = PA::$app->getModules();
        $this->view->modules = $modules;
        $rule = $this->item_id ? Menus::findFirst($this->item_id)->toArray() : [];
        if(!empty($rule['router'])) $rule['router'] = json_decode($rule['router'],1);
        $this->view->rule = $rule;
        $this->render('menus/edit');
    }
}
