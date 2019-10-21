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
use Phalcon\Db\Adapter\Pdo\Factory;
use Power\Controllers\AdminBaseController;
use PA;
use Power\Models\Roles;
use Power\Models\Rules;
use Tables\PluginsTableMenus;
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

    /**
     * 生成模型文件
     */
    public function make_model(){

    }

    /**
     * 生成菜单
     */
    public function generate_rule(){

    }

    /**
     * 从数据源同步表到Menus表中
     */
    public function sync_table(){
        $source_id   = $this->getParam('source_id');
        $source_info = PluginsTableSources::findFirst($source_id);
        $arr = [
            'host'     => $source_info->host,
            'dbname'   => $source_info->dbname,
            'port'     => $source_info->port,
            'username' => $source_info->user,
            'password' => $source_info->password,
            'adapter'  => $source_info->type,
        ];
        $db = Factory::load($arr);
        # 查出所有的表
        $tables = [];
        switch($source_info->type){ # todo 加入其它支持的数据库
            case 'mysql':
                $tables = $db->query('show tables')->fetchAll(PDO::FETCH_COLUMN);
                break;
            case 'sqlite':
                $tables = $db->query('select name from sqlite_master where type="table" order by name')->fetchAll(PDO::FETCH_COLUMN);
                break;
        }
        # 插入到指定位置
        foreach($tables as $table) {
            $menu_table = PluginsTableMenus::findFirst(['source_id=?0 and table_name=?1', 'bind' => [$source_id, $table]]);
            if(!$menu_table){
                (new PluginsTableMenus)->create(['rule_id'=>null,'source_id'=>$source_id,'table_name'=>$table,'model_file'=>null]);
            }
        }
        $this->response->redirect($this->url('display',['item_id'=>$this->item_id,'action'=>'set','event'=>'setting']));
    }

    /**
     * 获得子事件的URL
     * @param array $param
     * @param string $method
     * @return string
     * @throws \Exception
     */
    private function getUrl(array $param, $method='GET'){
        return $this->url($method=='GET'?'display':'update', array_merge($this->params, $param));
    }

    /**
     * 设置主页面
     * @return mixed
     * @throws \Exception
     */
    public function settingsAction(){
        $this->title = 'Tables插件设置';
        if($this->getParam('command')){
             return $this->{$this->getParam('command')}();
        }
        
        $sync_table_url = $this->getUrl(['command'=>'sync_table']);
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
                          ['name'=>'dbname','text'=>'数据库名','sort'=>1,'filter'=>1],
                          ['name'=>'type','text'=>'类型','sort'=>1,'filter'=>1],
                          ['name'=>'host','text'=>'主机','sort'=>1,'filter'=>1],
                          ['name'=>'port','text'=>'端口','sort'=>1,'filter'=>1],
                          ['name'=>'user','text'=>'用户','sort'=>1,'filter'=>1],
                          ['name'=>'password','text'=>'密码','sort'=>1,'filter'=>1],
                          ['name'=>'path','text'=>'模型文件的目录','sort'=>1,'filter'=>1],
                          ['name'=>'status','text'=>'状态','sort'=>1,'show'=>0],
                    ]
                )->primary('id')->canEdit('操作')->editCallback(/** @lang JavaScript */
                    <<<OUT
                    (i,j) => {
                        console.log('编辑回调', i);
                        return '<a title="同步数据表" href="$sync_table_url/source_id/' + i.id + '"><i class="fa fa-random"></i> &nbsp;</a>' + j;
                    }
OUT
                )
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
                        ['name'=>'source_id','text'=>'所属数据源ID','show'=>0],
                        ['name'=>'source_name','text'=>'所属数据源','render'=>'i=>"["+i+"]"'],
                        ['name'=>'rule_id','text'=>'菜单ID','show'=>0],
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

    /**
     * 获得列表数据 Ajax
     */
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
            $data = array_map(function($v){
                $rule = Rules::findFirstByRuleId($v['rule_id']);
                $source = PluginsTableSources::findFirstById($v['source_id']);
                $v['rule_name']   = $rule ? $rule->name : '';
                $v['source_name'] = $source ? $source->name : '';
                if(!$v['model_file']){
                    $url1 = $this->getUrl(['command'=>'make_model']);
                    $v['model_file'] = '<a href="'.$url1.'">生成模型</a>';
                }
                $url2 = $this->getUrl(['command'=>'generate_rule']);
                $v['action'] = '<a href="'.$url2.'">启用</a>';
                return $v;
            },$data->toArray());

        }else{
            $data = array_map(function($v){
                if(!$v['status']){
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

    /**
     * 显示列表
     * @throws \Exception
     */
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

    /**
     * 更新
     * @throws \Exception
     */
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

    /**
     * 删除
     */
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