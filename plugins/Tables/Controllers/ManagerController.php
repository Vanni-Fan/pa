<?php
namespace plugins\Tables\Controllers;

use HtmlBuilder\Components;
use HtmlBuilder\Element;
use HtmlBuilder\Forms;
use HtmlBuilder\Layouts;
use HtmlBuilder\Parser\AdminLte\Parser;
use PDO;
use Phalcon\Db\Adapter\Pdo\Factory;
use Phalcon\Db\Column;
use Phalcon\Text;
use plugins\DataSource\Models\DataSources;
use plugins\Tables\Models\TablesFields;
use plugins\Tables\Models\TablesMenus;
use Power\Controllers\AdminBaseController;
use PA;
use Power\Models\Permissions;
use Power\Models\Roles;
use Power\Models\menus;
use Power\Models\Users;
use Tables\System\PluginsTableMenus;
use Tables\System\PluginsTableSources;
use AdminHelper;


class ManagerController extends AdminBaseController
{
    private $params = [];
    public function initialize()
    {
//        $this->page_size = 10;
//        PA::$loader->registerNamespaces([
//            'Tables' => POWER_DATA . 'TablesPlugins/'
//        ]);
//        PA::$loader->register();
        $this->params = $this->getParam();
        unset($this->params['Rule']);
        $this->template_path = POWER_BASE_DIR . 'plugins/Tables/views/templates/';
        return parent::initialize();
    }

    /**
     * 生成模型文件
     */
//    public function make_model(){
//        $menu_id   = $this->getParam('id','int');
//        $menu_info = PluginsTableMenus::query()
//                                      ->createBuilder()
//                                      ->from(['s' => PluginsTableSources::class])
//                                      ->join(PluginsTableMenus::class, 's.id=m.source_id', 'm')
//                                      ->where('m.id=?0', [$menu_id])
//                                      ->columns('m.table_name,m.model_file,s.name,s.is_system,s.path,s.host,s.dbname,s.port,s.user,s.password,s.type')
//                                      ->getQuery()
//                                      ->execute();
//        if($menu_info){
//            $menu_info = $menu_info[0];
//            $is_system = $menu_info->is_system;
//            if(!$menu_info->path){
//                $path = $is_system ? (POWER_BASE_DIR.'models/') : (POWER_DATA.'TablesPlugins/Tables/'.$menu_info->dbname.'/');
//            }else{
//                $path = $menu_info->path.'/';
//            }
//            is_dir($path) || mkdir($path, 0777, true);
//            $class_name  = Text::camelize($menu_info->table_name);
//            $target_file = $path . $class_name . '.php';
//            if(!file_exists($target_file)){
//                if($is_system){
//                    $tmp_content =
//                        "<?php\n".
//                        "/** The file is generated automatically by TablePlugin */\n".
//                        "namespace Power\Models;\n".
//                        "class $class_name extends PowerModelBase{\n}";
//                }else{
//                    $temp_file   = realpath(__DIR__.'/../ModelTemplate.php');
//                    $db_connect  = [
//                        'host'     => $menu_info->host,
//                        'dbname'   => $menu_info->dbname,
//                        'port'     => $menu_info->port,
//                        'username' => $menu_info->user,
//                        'password' => $menu_info->password,
//                        'adapter'  => $menu_info->type,
//                    ];
//                    $tmp_content = str_replace(
//                        ['__DB_NAME__','__TABLE_NAME__','__MODEL_NAME__','__DB_INFO__'],
//                        [$menu_info->dbname, $menu_info->table_name, $class_name, var_export($db_connect,1)],
//                        file_get_contents($temp_file)
//                    );
//                }
//                file_put_contents($target_file, $tmp_content);
//                PluginsTableMenus::find($menu_id)->assign(['model_file'=>realpath($target_file)])->update();
//            }
//
////            print_r($menu_info->toArray());
//            echo $target_file,"\n\n";
////            echo PA::$db->getSQLStatement();
////            exit;
//        }else{
//            throw new \Exception('数据不存在');
//        }
//        $this->response->redirect($this->url('display',['item_id'=>$this->item_id,'action'=>'set','event'=>'setting']));
//    }

    
//    public function disable_rule(){
//        $menu_id = $this->getParam('id','int');
//        $menu_info = PluginsTableMenus::findFirst($menu_id);
//        if($menu_info) {
//            Menus::deleteRule($menu_info->menu_id);
//            $menu_info->menu_id = null;
//            $menu_info->save();
//        }
//        $this->response->redirect($this->url('display',['item_id'=>$this->item_id,'action'=>'set','event'=>'setting']));
//    }
    /**
     * 生成菜单
     */
//    public function generate_rule(){
//        $menu_id = $this->getParam('id','int');
//        $menu_info = PluginsTableMenus::query()
//                                      ->createBuilder()
//                                      ->from(['s'=>PluginsTableSources::class])
//                                      ->join(PluginsTableMenus::class,'s.id=m.source_id','m')
//                                      ->where('m.id=?0',[$menu_id])
//                                      ->columns('m.table_name,m.menu_id,m.model_file,s.name,s.is_system,m.source_id,s.dbname')
//                                      ->getQuery()
//                                      ->execute();
//        if(!$menu_info) throw new \Exception('数据不存在');
//        # 创建菜单，并赋予管理员
//        $menu_info = $menu_info[0];
//        if(!$menu_info->menu_id){
//            $parent = Menus::findFirst(['params=?0','bind'=>['['.$menu_info->source_id.']'],'columns'=>'menu_id']);
//            if($parent){
//                $parent_id = $parent['menu_id'];
//            }else{
//                $p_rule = new menus;
//                $p_rule->assign(
//                    [
//                        'name'         => '[' . $menu_info->name . ']库',
//                        'params'       => '[' . $menu_info->source_id.']',
//                        'icon'         => 'fa fa-database',
//                        'parent_id'    => 0,
//                        'is_enabled'      => 1,
//                        'created_time' => time(),
//                        'created_user' => $this->getUserId(),
//                    ]
//                )->create();
//                $parent_id = $p_rule->menu_id;
//            }
//            $rule = Menus::getInstance();
//            $rule->assign(
//                [
//                    'name'         => '[' . $menu_info->table_name . ']表',
//                    'router'       => '{"controller":"tables","action":"index","namespace":"plugins\\\\Tables\\\\Controllers","priority":10}',
//                    'params'       => '{"source_id":' . $menu_info->source_id . ',"table":"' . $menu_info->table_name . '"}',
//                    'icon'         => 'fa fa-table',
//                    'parent_id'    => $parent_id,
//                    'is_enabled'      => 1,
//                    'created_time' => time(),
//                    'created_user' => $this->getUserId(),
//                ]
//            )->create();
//            # 更新管理员权限
//            $role = Roles::findFirstByRoleId(1);
//            $role_menus = json_decode($role->menus,1);
//            $role_menus[$rule->menu_id] = 255;
//            $role->menus = json_encode($role_menus,JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
//            $role->save();
//
//            # 更新菜单ID
//            $menu = PluginsTableMenus::findFirst($menu_id);
//            $menu->menu_id = $rule->menu_id;
//            $menu->save();
//        }
//        $this->response->redirect($this->url('display',['item_id'=>$this->item_id,'action'=>'set','event'=>'setting']));
//    }

    /**
     * 从数据源同步表到Menus表中
     */
//    public function sync_table(){
//        $source_id   = $this->getParam('source_id');
//        $source_info = PluginsTableSources::findFirst($source_id);
//        $arr = [
//            'host'     => $source_info->host,
//            'dbname'   => $source_info->dbname,
//            'port'     => $source_info->port,
//            'username' => $source_info->user,
//            'password' => $source_info->password,
//            'adapter'  => $source_info->type,
//        ];
//        $db = Factory::load($arr);
//        # 查出所有的表
//        $tables = [];
//
//        switch($source_info->type){ # todo 加入其它支持的数据库
//            case 'mysql':
//                $tables = $db->query('show tables')->fetchAll(PDO::FETCH_COLUMN);
//                break;
//            case 'sqlite':
//                $tables = $db->query('select name from sqlite_master where type="table" order by name')->fetchAll(PDO::FETCH_COLUMN);
//                break;
//        }
//        # 插入到指定位置
//        foreach($tables as $table) {
//            $menu_table = PluginsTableMenus::findFirst(['source_id=?0 and table_name=?1', 'bind' => [$source_id, $table]]);
//            if(!$menu_table){
//                $model_file = null;
//                if($source_info->is_system){
//                    $model_file = realpath(POWER_BASE_DIR . 'models/'.Text::camelize($table).'.php');
//                }
//                (new PluginsTableMenus)->assign(['menu_id'=>null,'source_id'=>$source_id,'table_name'=>$table,'model_file'=>$model_file])->create();
//            }
//        }
//        $this->response->redirect($this->url('display',['item_id'=>$this->item_id,'action'=>'set','event'=>'setting']));
//    }

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
        if($this->getParam('command')) return $this->{$this->getParam('command')}();

        $this->title = 'Tables插件设置';

        [$sources,$iterator] = [[],DataSources::getSources()];
        iterator_apply($iterator,function($iterator, &$sources){
            $item = $iterator->current();
            $db   = DataSources::getDB($item);
            $sources[] = [
                'source_id' => $item['source_id'],
                'name'      => $item['name'],
                'tables'    => $db ? $db->listTables() : [],
            ];
            return true;
        },[$iterator, &$sources]);

        $this->view->sources = $sources;
        $this->view->get_menus_url = $this->getUrl(['command'=>'getMenuList','s_id'=>'S_ID','t_id'=>'T_ID']);
        $this->view->add_menus_url = $this->getUrl(['command'=>'addMenu','s_id'=>'S_ID','t_id'=>'T_ID']);
        $this->view->save_menus_url = $this->getUrl(['command'=>'saveMenu','s_id'=>'S_ID','t_id'=>'T_ID']);
        $this->view->all_icons = AdminHelper::getIcons();

        $this->view->all_menus = Menus::getFlatMenus();
        $this->render('manager/edit');

        return;

        
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
                ->updateApi($this->routerUrl('update',['namespace'=>'Power\\Controllers','controller'=>'menus'],['item_id'=>'{id}']))
                ->deleteApi($this->getUrl(['command'=>'delete','type'=>'menu','sub_command'=>'show','id'=>'{id}']))
                ->fields(
                    [
                        ['name'=>'id','text'=>'id','sort'=>1, 'filter'=>1],
                        ['name'=>'source_id','text'=>'所属数据源ID','show'=>0],
                        ['name'=>'source_name','text'=>'所属数据源','render'=>'i=>"["+i+"]"'],
                        ['name'=>'menu_id','text'=>'菜单ID','show'=>0],
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
     * 编辑：基本参数 > 提交
     */
    public function addMenu(){
        $data = $_POST;
        $cans = array_filter(
            $_POST,
            fn($v, $k) => stripos($k,'can')===0 && ($v==='1' || $v==='true'),
            ARRAY_FILTER_USE_BOTH
        );
        $time = time();
        $db   = (new Users)->getWriteConnection();
        $err  = '';
        $db->begin();
        try {
            if (!empty($_POST['id'])) { // 修改
                $obj = TablesMenus::findFirst((int)$_POST['id']);
                # 移动或修改菜单
                if($obj->parent_menu_id != $data['parent_menu_id']) {
                    $menu = Menus::findFirst($obj->menu_id);
                    $menu->name = $data['menu_name'];
                    $menu->parent_id = $data['parent_menu_id'];
                    $menu->save();
                }
            } else {  // 添加
                $obj = new TablesMenus();
                # 添加菜单
                $menu = new Menus;
                $menu->assign([
                    'name' => $data['menu_name'],
                    'router' => json_encode([
                        "priority"=> 10,
                        "namespace"=> "Power\\Controllers",
                        "controller"=> "Roles"
                     ]),
                    'params'=>json_encode([

                    ]),
                    'icon' => '',
                    'parent_id' => $data['parent_menu_id'] ?: null,
                    'is_enabled' => 1,
                    'created_user' => $this->userinfo['user_id'],
                    'updated_user' => $this->userinfo['user_id'],
                    'created_time' => $time,
                    'updated_time' => $time,
                ])->save();
                # 分配权限
                (new Permissions)->assign([
                    'role_id' => $this->userinfo['role_id'],
                    'type' => 'menu',
                    'menu_id' => $menu->menu_id,
                    'value' => 255,
                    'created_user' => $this->userinfo['user_id'],
                    'updated_user' => $this->userinfo['user_id'],
                    'created_time' => $time,
                    'updated_time' => $time,
                ])->save();
                $data['menu_id'] = $menu->menu_id;
            }
            $data['table']     = $this->getParam('t_id');
            $data['title']     = $data['title'] ?: $data['name'] ?? $data['menu_name'];
            $data['source_id'] = (int)$this->params['s_id'];
            $data['canMin']    = isset($cans['canMin'])    ? 1 : 0;
            $data['canClose']  = isset($cans['canClose'])  ? 1 : 0;
            $data['canSelect'] = isset($cans['canSelect']) ? 1 : 0;
            $data['canEdit']   = isset($cans['canEdit'])   ? 1 : 0;
            $data['canAppend'] = isset($cans['canAppend']) ? 1 : 0;
            $data['canDelete'] = isset($cans['canDelete']) ? 1 : 0;
            $data['canFilter'] = isset($cans['canFilter']) ? 1 : 0;
            $obj->assign($data)->save();
            $data['id'] = (int)$obj->id;
            # 创建默认字段
            if(empty($_POST['id'])) {
                $model = DataSources::getModel($data['source_id'], $data['table']);
                $fields = $model->getModelsMetaData()->getDataTypes($model);
                $row = [
                    'table_id'=>$data['id'],
//                    'field'=>'',
//                    'name'=>'',
//                    'tooltip'=>'',
//                    'width'=>'',
//                    'sort'=>'',
//                    'filter'=>'',
//                    'show'=>'',
//                    'primary'=>'',
//                    'render'=>'',
//                    'type'=>'',
//                    'params'=>'',
//                    'icon'=>'',
//                    'class'=>'',
                ];
                foreach ($fields as $field=>$type) {
                    $row['name'] = $row['field'] = $field;

                    switch($type){
                        case Column::TYPE_INTEGER:
                        case Column::TYPE_BIGINTEGER:
                        case Column::BIND_PARAM_INT:
                        case Column::TYPE_FLOAT:
                        case Column::TYPE_DOUBLE:
                        case Column::TYPE_DECIMAL:
                        case Column::TYPE_BOOLEAN:
                            $row['type'] = 'number';
                            break;
                        case Column::TYPE_DATE:
                            $row['type'] = 'date';
                            break;
                        case Column::TYPE_TIMESTAMP:
                        case Column::TYPE_DATETIME:
                            $row['type'] = 'datetime';
                            break;
                        case Column::TYPE_TINYBLOB:
                        case Column::TYPE_BLOB:
                        case Column::TYPE_MEDIUMBLOB:
                        case Column::TYPE_LONGBLOB:
                        case Column::TYPE_JSONB:
                            $row['type'] = 'file';
                            break;
                        case Column::TYPE_VARCHAR:
                        case Column::TYPE_CHAR:
                        case Column::TYPE_TEXT:
                        case Column::TYPE_JSON:
                        default:
                            $row['type'] = 'text';
                            break;
                    }

                    $tf = DataSources::getModel(1, PA::$config->pa_db->prefix . 'tables_fields');
                    $tf->assign($row);
                    $tf->save();
                }
            }

            $db->commit();
        }catch(\Throwable $e){
            $err = $e->getMessage();
            echo $e->getTraceAsString();
            $db->rollback();
        }
        if($err) return $this->jsonOut(['error'=>$err]);
        $this->getMenuList();
    }

    /**
     * 编辑：字段设置 > 保存
     */
    public function setFields(){

    }

    public function getMenuList(){
        [$data, $_menus] = [[], TablesMenus::find(['source_id=?0 and table=?1','bind'=>[$this->params['s_id'], $this->params['t_id']]])];
        iterator_apply($_menus, function (&$data, $interator){
            $item            = $interator->current()->toArray();
//            $item['menu_name']    = $item['menu_name'];
            $item['menu_id'] = (int)$item['menu_id'];
            $item['filters'] = $item['filters'] ? json_decode($item['filters'], 1) : [];
            $item['fields']  = TablesFields::find(['table_id=?0', 'bind'=>[$item['id']]])->toArray();
            $item['canMin']    = (int)$item['canMin'];
            $item['canClose']  = (int)$item['canClose'];
            $item['canSelect'] = (int)$item['canSelect'];
            $item['canEdit']   = (int)$item['canEdit'];
            $item['canAppend'] = (int)$item['canAppend'];
            $item['canDelete'] = (int)$item['canDelete'];
            $item['canFilter'] = (int)$item['canFilter'];
            $data[]          = $item;
            return true;
        },[&$data, $_menus]);

        $this->jsonOut($data);
    }
    /**
     * 获得列表数据 Ajax
     */
//    public function getList(){
//        $size  = $_POST['limit']['size']??$this->page_size;
//        $page  = $_POST['limit']['page']??1;
//        $where = [
//            'conditions' => '',
//            'limit'      => $size,
//            'offset'     => ($page - 1) * $size,
//            'bind'       => []
//        ];
//
//        # 条件
//        $model = $this->params['type'] == 'menu' ? PluginsTableMenus::class : PluginsTableSources::class;
//        if(isset($_POST['filters'])){
//            call_user_func_array([$model,'parseWhere'],[['where'=>&$_POST['filters']], &$where]);
//        }
////        print_r($where);
//        # 排序
//        if(isset($_POST['sort'])){
//            $where['order'] = '';
//            foreach($_POST['sort'] as $sort){
//                $where['order'] .= $sort['name'].' '.$sort['type'].',';
//            }
//            $where['order'] = substr($where['order'],0,-1);
//        }
//
//        $data = call_user_func([$model,'find'], $where);
//        if($this->params['type'] == 'menu'){
//            $data = array_map(function($v){
//                $rule = Menus::findFirstByMenuId($v['menu_id']);
//                $source = PluginsTableSources::findFirstById($v['source_id']);
//                $v['rule_name']   = $rule ? $rule->name : '';
//                $v['source_name'] = $source ? $source->name : '';
//                if(!$v['model_file']){
//                    $url1 = $this->getUrl(['command'=>'make_model','id'=>$v['id']]);
//                    $v['model_file'] = '<a href="'.$url1.'">生成模型</a>';
//                }
//                if(!$v['menu_id']){
//                    $url2 = $this->getUrl(['command'=>'generate_rule','id'=>$v['id']]);
//                    $v['action'] = '<a href="'.$url2.'">启用</a>';
//                }else{
//                    $url2 = $this->getUrl(['command'=>'disable_rule','id'=>$v['id']]);
//                    $v['action'] = '<a href="'.$url2.'">禁用</a>';
//                    $url3 = $this->routerUrl('display',['controller'=>'menus','namespace'=>'Power\\Controllers'],['item_id'=>$v['menu_id']]);
//                    $v['rule_name'] = '<a href="'.$url3.'">' . $v['rule_name'] . '</a>';
//                }
//                return $v;
//            },$data->toArray());
//
//        }else{
//            $data = array_map(function($v){
//                if(!$v['status']){
//                }
//                $v['password'] = '*******';
//                unset($v['status']);
//                return $v;
//            },$data->toArray());
//        }
//        $this->jsonOut(
//            [
//                'list'=>$data,'total'=>call_user_func(
//                    [$model,'count'],
//                    ['conditions'=>$where['conditions'],'bind'=>$where['bind']]
//                ),
//                'page'=>$page,
//                'size'=>$size,
//                'query'=>[
//                    'filter'=>$_POST['filters']??[],
//                    'limit'=>$_POST['limit']??['page'=>$page, 'size'=>$size]
//                ]
//            ]
//        );
//    }

    /**
     * 显示列表
     * @throws \Exception
     */
//    public function show(){
//        $model = $this->params['type'] == 'menu' ? \Tables\PluginsTableMenus::class : \Tables\PluginsTableSources::class;
//        $default = $this->params['sub_command'] == 'edit' ? call_user_func([$model,'findFirst'], $this->params['id']) : new \stdClass();
//        $parser = new Parser();
//        if($this->params['type'] == 'source') {
//            $this->view->content = $parser->parse(
//                Forms::form($this->getUrl(['command' => 'update']))->add(
//                    Layouts::box(
//                        Layouts::columns()->column(
//                            Element::create('div')->add(
//                                Forms::input('name', '名称',$default->name??'')->required(),
//                                Forms::input('host', '主机', $default->host??'')->required()->inputMask("'alias':'ip'"),
//                                Forms::input('user', '用户', $default->user??'')->required(),
//                                Forms::select('type','类型', $default->type??'')->choices([
//                                                                                            ['text' => 'MySQL', 'value' => 'mysql'],
//                                                                                            ['text' => 'SQLite', 'value' => 'sqlite'],
//                                                                                            ['text' => 'PostgreSQL', 'value' => 'postgresql'],
//                                                                                        ])->required(),
//                            )
//                            , 6
//                        )->column(
//                            Element::create('div')->add(
//                                Forms::input('dbname', '数据库名称',$default->dbname??'')->required(),
//                                Forms::input('port', '端口', $default->port??'')->required()->subtype('number'),
//                                Forms::input('password', '密码', $default->password??'')->required()->subtype('password'),
//                                Forms::input('path','模型保存目录',$default->path??'')->tooltip('Model类文件保存的目录')
//                            )
//                            , 6
//                        ),
//                        '编辑数据源',
//                        Element::create('div')->add(
//                            Forms::button('返回')->on('click','window.history.back()')->style('default'),
//                            Forms::button('提交')->action('submit')->class('pull-right')
//                        )
//                    )
//                )
//            );
//        }else{
//            $menus = array_map(function($v){
//                return ['text'=>str_repeat('&nbsp;　&nbsp;',$v['level']) . $v['name'], 'value'=>$v['menu_id']];
//            },Menus::getFlatMenus());
//            $sources = PluginsTableSources::find(['columns'=>'id as value,name as text'])->toArray();
//            $this->view->content = $parser->parse(
//                Forms::form($this->getUrl(['command' => 'update']))->add(
//                    Layouts::box(
//                        Element::create('div')->add(
//                            Forms::select('menu_id','上级目录菜单', $default->menu_id??'','select2')
//                                 ->choices($menus)
//                                 ->required()
//                                 ->description(
//                                     '如果找不到对应的菜单目录，请在《<a href="'
//                                     .$this->routerUrl('index',['namespace'=>'Power\\Controllers','controller'=>'menus'])
//                                     .'">系统管理->权限管理</a>》中<a href="'
//                                     .$this->routerUrl('new',['namespace'=>'Power\\Controllers','controller'=>'menus'])
//                                     .'">创建菜单</a>。'
//                                 )
//                                 ->tooltip('将此功能放到什么菜单位置下面'),
//                            Forms::input('name', '菜单名称', $default->table_name??'')->required()->tooltip('表名必须为中文'),
//                            Forms::select('source_id','选择数据源', $default->source_id??'','select2')
//                                 ->choices($sources)
//                                 ->required()
//                                 ->description(
//                                     '数据源信息在<a href="'
//                                     .$this->getUrl(['sub_command'=>'new','type'=>'source'])
//                                     .'">数据源管理</a>中创建'
//                                 ),
//                            Forms::input('table_name', '表名', $default->table_name??'')->required()->tooltip('表名必须为中文')
//                        ),'编辑',
//                        Element::create('div')->add(
//                            Forms::button('返回')->on('click','window.history.back()')->style('default'),
//                            Forms::button('提交')->action('submit')->class('pull-right')
//                        )
//                    )
//                )
//            );
//        }
//        $parser->setResources($this);
//        $this->render('manager/source-edit');
//    }

    /**
     * 更新
     * @throws \Exception
     */
//    public function update(){
//        if($this->params['type'] == 'menu'){
//            if($this->params['sub_command']=='new'){
//                $model = new PluginsTableMenus();
//                $model->assign($_POST)->create();
//                # 插入一个菜单
//                $rule = new menus;
//                $id = $rule->assign([
//                    'name'      => $_POST['name'],
//                    'router'    => '{"controller":"tables","action":"index","namespace":"plugins\\\\Tables\\\\Controllers","priority":10}',
//                    'params'    => '{"source_id":' . $_POST['source_id'] . ',"table":"' . $_POST['table_name'] . '"}',
//                    'parent_id' => $_POST['menu_id'],
//                    'index'     => 0,
//                    'enabled'   => 1,
//                    'icon'      => 'fa fa-table',
//                    'created_time' => time(),
//                    'created_user' => $this->getUserId(),
//                ])->create();
//                # 更新管理员权限
//                $role = Roles::findFirstByRoleId(1);
//                $role_menus = json_decode($role->menus,1);
//                $role_menus[$rule->menu_id] = 255;
//                $role->menus = json_encode($role_menus,JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
//                $role->save();
//                # 跳转回去
////                $this->response->redirect($this->url('index',['action'=>'set','event'=>'setting']),true);
//            }
//        }else {
//            if($this->params['sub_command']=='new'){
//                $model = new PluginsTableSources();
//                $_POST['status'] = 1;
//                $model->assign($_POST)->create();
//            }else{
//                $data = PluginsTableSources::find($this->getParam('id'));
//                $data->assign($_POST)->update();
//            }
//        }
//
//        $this->response->redirect($this->url('display',['item_id'=>$this->item_id,'action'=>'set','event'=>'setting']));
//    }

    /**
     * 删除
     */
//    public function delete(){
//        $model = $this->params['type'] == 'menu' ? PluginsTableMenus::class : PluginsTableSources::class;
//        if(strpos($this->params['id'],',')){
//            $ids = explode(',',$this->params['id']);
//        }else{
//            $ids = [$this->params['id']];
//        }
//        foreach($ids as $id){
//            call_user_func([$model,'findFirst'], $id)->delete();
//        }
//        return $this->getList();
//    }
}