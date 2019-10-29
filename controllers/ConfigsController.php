<?php
namespace Power\Controllers;
use HtmlBuilder\Components;
use HtmlBuilder\Layouts;
use HtmlBuilder\Parser\AdminLte\Parser;
use Power\Models\Configs;
use Power\Models\menus;

class ConfigsController extends AdminBaseController {
    public $page_size =10;
    # 获取列表的Ajax方法
    public function listAction(){
        $size  = $_POST['limit']['size']??$this->page_size;
        $page  = $_POST['limit']['page']??1;
        $where = [
            'conditions' => '',
            'limit'      => $size,
            'offset'     => ($page - 1) * $size,
            'bind'       => []
        ];

        if(isset($_POST['sort'])){
            $where['order'] = '';
            foreach($_POST['sort'] as $sort){
                $where['order'] .= $sort['name'].' '.$sort['type'].',';
            }
            $where['order'] = substr($where['order'],0,-1);
        }

        # 条件
        if(isset($_POST['filters'])) Configs::parseWhere(['where'=>$_POST['filters']],$where);

        $data = [];
        foreach(Configs::find($where) as $row){
            $d = $row->toArray();
//            var_dump($row->Menu->toArray());
            $d['menu_name']    = $row->Menu ? $row->Menu->name : '';
            $d['created_name'] = $row->CreatedUser ? $row->CreatedUser->nickname : '';
            $d['updated_name'] = $row->UpdatedUser ? $row->UpdatedUser->nickname : '';
            $data[] = $d;
        }

        $this->jsonOut(
            [
                'list'=>$data,
                'total'=>Configs::count(['conditions'=>$where['conditions'],'bind'=>$where['bind']]),
//                'total'=>500,
                'page'=>(int)$page,
                'size'=>(int)$size,
                'query'=>[
                    'filter'=>$_POST['filters']??[],
                    'limit'=>$_POST['limit']??['page'=>$page, 'size'=>$size]
                ]
            ]
        );
    }

    # 列表页面
    public function indexAction(){
        $this->title = '配置管理';
        $this->subtitle = '这些配置可用于权限设置，或者个人配置';
        $parser = new Parser();
        $this->view->contents = $parser->parse(
            Components::table('配置列表')->fields([
                ['name'=>'config_id', 'text'=>'配置ID','sort'=>1, 'filter'=>1],
                ['name'=>'type', 'text'=>'配置类型','sort'=>1, 'filter'=>1,'render'=>'v=>v=="rule"?"权限":"属性"','class'=>'text-center'],
                ['name'=>'menu_id', 'text'=>'所属菜单ID','sort'=>1, 'show'=>0, 'filter'=>1],
                ['name'=>'menu_name', 'text'=>'所属菜单','sort'=>1, 'filter'=>0],
                ['name'=>'is_action_name', 'text'=>'是否有同名的Action','show'=>0, 'sort'=>1, 'filter'=>1],
                ['name'=>'name', 'text'=>'名称','sort'=>1, 'filter'=>1],
                ['name'=>'var_name', 'text'=>'变量名','sort'=>1, 'filter'=>1],
                ['name'=>'var_default', 'text'=>'变量默认值','sort'=>1, 'filter'=>1],
                ['name'=>'var_type', 'text'=>'变量类型','sort'=>1, 'filter'=>1],
                ['name'=>'options', 'text'=>'可选项','sort'=>1, 'filter'=>1],
                ['name'=>'options_type', 'text'=>'可选项类型','sort'=>1, 'filter'=>1],
                ['name'=>'is_enabled', 'text'=>'是否可用','sort'=>1, 'filter'=>1,'render'=>'v=>v==1?"已启用":"已禁用"','class'=>'text-center'],
                ['name'=>'remark', 'text'=>'备注','sort'=>1, 'show'=>0, 'filter'=>1],
                ['name'=>'created_time', 'text'=>'创建时间','sort'=>1, 'show'=>0, 'filter'=>1, 'render'=>'i=>i?new Date(i*1000).toLocaleString():""'],
                ['name'=>'updated_time', 'text'=>'更新时间','sort'=>1, 'show'=>0, 'filter'=>1, 'render'=>'i=>i?new Date(i*1000).toLocaleString():""'],
                ['name'=>'created_user', 'text'=>'创建者ID','sort'=>1, 'show'=>0, 'filter'=>1],
                ['name'=>'updated_user', 'text'=>'更新者ID','sort'=>1, 'show'=>0, 'filter'=>1],
                ['name'=>'created_name', 'text'=>'创建者','show'=>0, 'filter'=>0],
                ['name'=>'updated_name', 'text'=>'更新者','show'=>0, 'filter'=>0],
            ])->canEdit(true)->primary('config_id')->canMin(false)
            ->queryApi($this->url('list'))
            ->createApi($this->url('append'))
            ->deleteApi($this->url('delete',['item_id'=>'_ID_']))
            ->updateApi($this->url('update',['item_id'=>'_ID_']))
            ->description(
                [
                    '点击[字段]可以展示或隐藏更多的列',
                    '[筛选]功能可以帮您精确查找出内容',
                    '配置可以绑定在具体的菜单上面，也可以不绑定菜单，不绑定在菜单上面的配置是全局配置'
                ][random_int(0,2)]
            )
        );
        $parser->setResources($this);
        return $this->render(null);

        $extends = Configs::find(['type=?0','bind'=>[$this->type],'order'=>'menu_id'])->toArray();
        $global  = $menus = [];
        foreach($extends as $extend){
            if($extend['menu_id']){
                $rule_info = [];
                Menus::getParentIds($extend['menu_id'], $rule_info);
                $extend['rule_names'] = implode('>', array_map(function($v){ return $v['name']; }, $rule_info));
                $menus[]  = $extend;
            }else{
                $global[] = $extend;
            }
        }
        $this->view->menus  = $menus;
        $this->view->global = $global;
        $this->render();
    }
    
    # 展示
    public function newAction(){ $this->displayAction(); }
    public function displayAction(){
        $this->title = $this->item_id ? '编辑配置' : '添加配置';
        $this->subtitle = '这些配置可用于权限设置，或者个人配置';
        $this->view->menus = Menus::getFlatMenus();
        if($this->item_id){
            $this->view->data = Configs::findFirst($this->item_id)->toArray();
        }
        $this->render('Configs/Edit');
    }
    
    # 修改
    public function appendAction(){ $this->updateAction(); }
    public function updateAction(){
        $_POST['options_type'] = $_POST['options_type'] ?: null;
        $_POST['menu_id'] = $_POST['menu_id'] ?: null;
        $_POST['is_action_name'] = $this->request->getPost('is_action_name','int',0);
        if($this->item_id){
            $extend = Configs::findFirst($this->item_id);
            $extend->update($_POST);
        }else{
            $extend = new Configs();
            $extend->create($_POST);
        }
        $this->response->redirect($this->url(),true);
    }
    
    # 删除
    public function deleteAction(){
        if(is_array($this->item_id)){
            $where = [
                'conditions' => 'config_id IN ({ids:array})',
                'bind' => [
                    'ids'=>$this->item_id
                ]
            ];
        }else{
            $where = $this->item_id;
        }
        Configs::find($where)->delete();
        return $this->listAction();
    }
}
