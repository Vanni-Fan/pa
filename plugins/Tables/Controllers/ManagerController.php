<?php
namespace plugins\Tables\Controllers;
use HtmlBuilder\Components;
use HtmlBuilder\Components\Table;
use HtmlBuilder\Element;
use HtmlBuilder\Forms;
use HtmlBuilder\Layouts;
use HtmlBuilder\Parser\AdminLte\Parser;
use HtmlBuilder\Validate;
use PDO;
use Phalcon\Db\ColumnInterface;
use Power\Controllers\AdminBaseController;
use PA;
use Power\Models\Roles;
use Power\Models\Rules;
use Power\Models\Users;
use Tables\PluginsTableSources;



class ManagerController extends AdminBaseController
{
    private $params = [];
    public function initialize()
    {
        $this->page_size = 10;
        PA::$loader->registerNamespaces([
            'Tables' => POWER_DATA . 'TablesPlugins/'
        ]);
        PA::$loader->register();
        $this->params = $this->getParam();
        unset($this->params['Rule']);
        return parent::initialize();
    }

    private function getUrl(array $param, $method='GET'){
        return $this->url($method=='GET'?'display':'update', array_merge($this->params, $param));
    }

    public function settingsAction(){
        $this->title = 'Tables插件设置';
        if($this->getParam('command')){
             return $this->{$this->getParam('command')}();
        }
        
        $parser = new Parser();
        $this->view->content = $parser->parse(
            Components::table('数据源集合')
                ->query(['limit'=>['page'=>1,'size'=>$this->page_size]])
                ->queryApi($this->getUrl(['command'=>'getList','type'=>'source']))
                ->createApi($this->getUrl(['command'=>'show','type'=>'source','sub_command'=>'new']))
                ->updateApi($this->getUrl(['command'=>'show','type'=>'source','sub_command'=>'edit','id'=>'{id}']))
                ->deleteApi($this->getUrl(['command'=>'delete','type'=>'source','sub_command'=>'show','id'=>'{id}']))
                ->fields(
                    [
                          ['name'=>'id','text'=>'id','sort'=>1, 'filter'=>1],
                          ['name'=>'name','text'=>'名称','sort'=>1,'filter'=>1],
                          ['name'=>'type','text'=>'类型','sort'=>1,'filter'=>1],
                          ['name'=>'host','text'=>'主机','sort'=>1,'filter'=>1],
                          ['name'=>'port','text'=>'端口','sort'=>1,'filter'=>1],
                          ['name'=>'user','text'=>'用户','sort'=>1,'filter'=>1],
                          ['name'=>'password','text'=>'密码','sort'=>1,'filter'=>1],
                          ['name'=>'path','text'=>'模型文件的目录','sort'=>1,'filter'=>1],
                          ['name'=>'status','text'=>'状态','sort'=>1,'show'=>0],
                    ]
                )->primary('id')
            ,
            Components::table('数据表集合')
                ->query(['limit'=>['page'=>1,'size'=>$this->page_size]])
                ->queryApi($this->getUrl(['command'=>'getList','type'=>'menu']))
                ->createApi($this->getUrl(['command'=>'show','type'=>'menu','sub_command'=>'new']))
                ->updateApi($this->routerUrl('update',['namespace'=>'Power\\Controllers','controller'=>'rules'],['item_id'=>'{id}']))
                ->deleteApi($this->getUrl(['command'=>'delete','type'=>'menu','sub_command'=>'show','id'=>'{id}']))
                ->fields(
                    [
                        ['name'=>'id','text'=>'id','sort'=>1, 'filter'=>1],
                        ['name'=>'source_id','text'=>'所属数据源','show'=>0],
                        ['name'=>'source_name','text'=>'所属数据源'],
                        ['name'=>'rule_id','text'=>'菜单名称','show'=>0],
                        ['name'=>'rule_name','text'=>'菜单名称'],
                        ['name'=>'table_name','text'=>'表名','sort'=>1, 'filter'=>1],
                        ['name'=>'model_file','text'=>'模型文件','filter'=>1],
                        ['name'=>'action','text'=>'操作','class'=>'text-center']
                    ]
                )->canEdit(false)
                 ->canDelete(false)
                 ->selectMode('')
                 ->description('在【操作】栏目中【启动编辑】，将生成新的权限菜单页面，在里面可以对表进行增删改查。默认菜单只分配给管理员！')
        );
        
        $parser->setResources($this);
        $this->render();
    }

    public function getList(){
        $size  = $_POST['limit']['size']??$this->page_size;
        $page  = $_POST['limit']['page']??1;
        $where = [
            'conditions' => '',
            'limit'      => $size,
            'offset'     => ($page - 1) * $size,
            'bind'       => []
        ];

        # 条件
        $model = $this->params['type'] == 'menu' ? \Tables\PluginsTableMenus::class : \Tables\PluginsTableSources::class;
        if(isset($_POST['filters'])){
            call_user_func_array([$model,'parseWhere'],[['where'=>&$_POST['filters']], &$where]);
//            $model->parseWhere($_POST['filters'], $where);
        }
//        print_r($where);
        # 排序
        if(isset($_POST['sort'])){
            $where['order'] = '';
            foreach($_POST['sort'] as $sort){
                $where['order'] .= $sort['name'].' '.$sort['type'].',';
            }
            $where['order'] = substr($where['order'],0,-1);
        }
        
        $data = call_user_func([$model,'find'], $where);
        if($this->params['type'] == 'menu'){
//            $data = [];
            $data = [
                ['id'=>1,'source_id'=>'','source_name'=>'系统源','rule_id'=>1,'rule_name'=>'<a href="/admin/menu/4/index">Roles表数据</a>','table_name'=>'roles','model_file'=>'Roles.php','action'=>'<a href="">禁用</a>'],
                ['id'=>2,'source_id'=>'','source_name'=>'系统源','rule_id'=>1,'rule_name'=>'','table_name'=>'rules','model_file'=>'Rules.php','action'=>'<a href="">开启</a>'],
                ['id'=>2,'source_id'=>'','source_name'=>'系统源','rule_id'=>1,'rule_name'=>'<a href="/admin/menu/7/index">系统配置</a>','table_name'=>'configs','model_file'=>'Configs.php','action'=>'<a href="">启用</a>'],
                ['id'=>3,'source_id'=>'','source_name'=>'AAA','rule_id'=>1,'rule_name'=>'','table_name'=>'aaa','model_file'=>'<a href="">生成模型</a>','action'=>''],
                ['id'=>4,'source_id'=>'','source_name'=>'AAA','rule_id'=>1,'rule_name'=>'','table_name'=>'aaa','model_file'=>'<a href="">生成模型</a>','action'=>''],
                ['id'=>4,'source_id'=>'','source_name'=>'AAA','rule_id'=>1,'rule_name'=>'','table_name'=>'aaa','model_file'=>'aaa.php','action'=>'<a href="">启用</a>'],
                ['id'=>4,'source_id'=>'','source_name'=>'AAA','rule_id'=>1,'rule_name'=>'','table_name'=>'aaa','model_file'=>'','action'=>''],
            ];
//            $data = array_map(function($v){
//                $rule = Rules::findFirstByRuleId($v['rule_id']);
//                $source = PluginsTableSources::findFirstById($v['source_id']);
//                $v['rule_id'] = $rule ? $rule->name : $v['rule_id'];
//                $v['source_id'] = $source ? $source->name : $v['source_id'];
//                return $v;
//            },$data->toArray());
        }else{
            $data = array_map(function($v){
                if(!$v['status']){
                    $v['canEdit'] = 0;
                    $v['canDelete'] = 0;
                }
                $v['password'] = '*******';
                unset($v['status']);
                return $v;
            },$data->toArray());
        }
        $this->jsonOut(
            [
                'list'=>$data,'total'=>call_user_func(
                    [$model,'count'],
                    ['conditions'=>$where['conditions'],'bind'=>$where['bind']]
                ),
                'page'=>$page,
                'size'=>$size,
                'query'=>[
                    'filter'=>$_POST['filters']??[],
                    'limit'=>$_POST['limit']??['page'=>$page, 'size'=>$size]
                ]
            ]
        );
    }

    public function show(){
        $model = $this->params['type'] == 'menu' ? \Tables\PluginsTableMenus::class : \Tables\PluginsTableSources::class;
        $default = $this->params['sub_command'] == 'edit' ? call_user_func([$model,'findFirst'], $this->params['id']) : new \stdClass();
        $parser = new Parser();
        if($this->params['type'] == 'source') {
            $this->view->content = $parser->parse(
                Forms::form($this->getUrl(['command' => 'update']))->add(
                    Layouts::box(
                        Layouts::columns()->column(
                            Element::create('div')->add(
                                Forms::input('name', '名称',$default->name??'')->required(),
                                Forms::input('host', '主机', $default->host??'')->required()->inputMask("'alias':'ip'"),
                                Forms::input('user', '用户', $default->user??'')->required(),
                                Forms::input('path','模型保存目录',$default->path??'')->required()->tooltip('Model类文件保存的目录')
                            )
                            , 6
                        )->column(
                            Element::create('div')->add(
                                Forms::select('type','类型', $default->type??'')->choices([
                                                   ['text' => 'MySQL', 'value' => 'mysql'],
                                                   ['text' => 'SQLite', 'value' => 'sqlite'],
                                                   ['text' => 'PostgreSQL', 'value' => 'postgresql'],
                                               ])->required(),
                                Forms::input('port', '端口', $default->port??'')->required()->subtype('number'),
                                Forms::input('password', '密码', $default->password??'')->required()->subtype('password'),
                            )
                            , 6
                        ),
                        '编辑数据源',
                        Element::create('div')->add(
                            Forms::button('返回')->on('click','window.history.back()')->style('default'),
                            Forms::button('提交')->action('submit')->class('pull-right')
                        )
                    )
                )
            );
        }else{
            $menus = array_map(function($v){
                return ['text'=>str_repeat('&nbsp;　&nbsp;',$v['level']) . $v['name'], 'value'=>$v['rule_id']];
            },Rules::getFlatMenus());
            $sources = PluginsTableSources::find(['columns'=>'id as value,name as text'])->toArray();
            $this->view->content = $parser->parse(
                Forms::form($this->getUrl(['command' => 'update']))->add(
                    Layouts::box(
                        Element::create('div')->add(
                            Forms::select('rule_id','上级目录菜单', $default->rule_id??'','select2')
                                 ->choices($menus)
                                 ->required()
                                 ->description(
                                     '如果找不到对应的菜单目录，请在《<a href="'
                                     .$this->routerUrl('index',['namespace'=>'Power\\Controllers','controller'=>'rules'])
                                     .'">系统管理->权限管理</a>》中<a href="'
                                     .$this->routerUrl('new',['namespace'=>'Power\\Controllers','controller'=>'rules'])
                                     .'">创建菜单</a>。'
                                 )
                                 ->tooltip('将此功能放到什么菜单位置下面'),
                            Forms::input('name', '菜单名称', $default->table_name??'')->required()->tooltip('表名必须为中文'),
                            Forms::select('source_id','选择数据源', $default->source_id??'','select2')
                                 ->choices($sources)
                                 ->required()
                                 ->description(
                                     '数据源信息在<a href="'
                                     .$this->getUrl(['sub_command'=>'new','type'=>'source'])
                                     .'">数据源管理</a>中创建'
                                 ),
                            Forms::input('table_name', '表名', $default->table_name??'')->required()->tooltip('表名必须为中文')
                        ),'编辑',
                        Element::create('div')->add(
                            Forms::button('返回')->on('click','window.history.back()')->style('default'),
                            Forms::button('提交')->action('submit')->class('pull-right')
                        )
                    )
                )
            );
        }
        $parser->setResources($this);
        $this->render('manager/source-edit');
    }

    public function update(){
        if($this->params['type'] == 'menu'){
            if($this->params['sub_command']=='new'){
                $model = new PluginsTableMenus();
                $model->create($_POST);
                # 插入一个菜单
                $rule = new Rules;
                $id = $rule->create([
                    'name'      => $_POST['name'],
                    'router'    => '{"controller":"tables","action":"index","namespace":"plugins\\\\Tables\\\\Controllers","priority":10}',
                    'params'    => '{"source_id":' . $_POST['source_id'] . ',"table":"' . $_POST['table_name'] . '"}',
                    'parent_id' => $_POST['rule_id'],
                    'index'     => 0,
                    'enabled'   => 1,
                    'icon'      => 'fa fa-table',
                    'data_source'  => 'tables',
                    'created_time' => time(),
                    'created_user' => $this->getUserId(),
                ]);
                # 更新管理员权限
                $role = Roles::findFirstByRoleId(1);
                $role_rules = json_decode($role->rules,1);
                $role_rules[$rule->rule_id] = 255;
                $role->rules = json_encode($role_rules,JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                $role->save();
                # 跳转回去
//                $this->response->redirect($this->url('index',['action'=>'set','event'=>'setting']),true);
            }
        }else {
            if($this->params['sub_command']=='new'){
                $model = new PluginsTableSources();
                $_POST['status'] = 1;
                $model->create($_POST);
            }else{
                $data = PluginsTableSources::find($this->getParam('id'));
                $data->update($_POST);
            }
        }

        $this->response->redirect($this->url('display',['item_id'=>$this->item_id,'action'=>'set','event'=>'setting']));
    }

    public function delete(){
        $model = $this->params['type'] == 'menu' ? \Tables\PluginsTableMenus::class : \Tables\PluginsTableSources::class;
        if(strpos($this->params['id'],',')){
            $ids = explode(',',$this->params['id']);
        }else{
            $ids = [$this->params['id']];
        }
        foreach($ids as $id){
            call_user_func([$model,'findFirst'], $id)->delete();
        }
        return $this->getList();
    }
}