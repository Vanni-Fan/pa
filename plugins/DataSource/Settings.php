<?php
namespace plugins\DataSource;
use PA;
use Phalcon\Db\Adapter\PdoFactory as DB;
use Phalcon\Db\Column;
use plugins\DataSource\Models\DataSources;
use Power\Models\Plugins;

class Settings {
    public static function setting(){
        PA::$dispatch->forward(
            [
                'controller'=>'Manager',
                'namespace'=>'plugins\DataSource\Controllers',
                'action'=>'settings',
            ]
        );
        return false;
    }
    public static function install($controller, $plugin){
        PA::$db->createTable(
            PA::$config->pa_db->prefix.'datasources',
            '',
            ['columns' => [
                new Column('source_id',['type' => Column::TYPE_INTEGER,'size'=> 10,'notNull'=> true,'autoIncrement' => true,'primary' => true,]),
                new Column('name',['type' => Column::TYPE_VARCHAR,'size' => 50]),
                new Column('adapter',['type' => Column::TYPE_VARCHAR,'size' => 10]),
                new Column('dbname',['type' => Column::TYPE_VARCHAR,'size' => 50]),
                new Column('username',['type' => Column::TYPE_VARCHAR,'size' => 50,'notNull'=> false]),
                new Column('password',['type' => Column::TYPE_VARCHAR,'size' => 50,'notNull'=> false]),
                new Column('prefix',['type' => Column::TYPE_VARCHAR,'size' => 50,'notNull'=> false]),
                new Column('host',['type' => Column::TYPE_VARCHAR,'size' => 50,'notNull'=> false]),
                new Column('port',['type'=>Column::TYPE_MEDIUMINTEGER,'notNull'=> false]),
                new Column('injection_name',['type' => Column::TYPE_VARCHAR,'size'=>10,'notNull'=> false]),
                new Column('bind_events_manager',['type'=>Column::TYPE_TINYINTEGER ,'size'=>1,'default'=>1]),
                new Column('remark',['type'=>Column::TYPE_TEXT,'notNull'=> false]),
                new Column('status',['type'=>Column::TYPE_TINYINTEGER ,'unsigned'=>true, 'default'=>0]),
                new Column('created_time',['type'=>Column::TYPE_INTEGER,'unsigned'=>true]),
                new Column('updated_time',['type'=>Column::TYPE_INTEGER,'unsigned'=>true]),
                new Column('created_user',['type'=>Column::TYPE_INTEGER,'unsigned'=>true]),
                new Column('updated_user',['type'=>Column::TYPE_INTEGER,'unsigned'=>true]),
            ]]
        );
        # 产生一个1000以上的自增
        $row = (new DataSources)->assign(['source_id'=>1000,'name'=>'tmp','adapter'=>'mysql','dbname'=>'tmp']);
        $row->create();
        $row->delete();

        return true;
    }
    public static function uninstall($controller, $plugin){
        return PA::$db->dropTable(PA::$config->pa_db->prefix.'datasources');
    }
    public static function autoload(){ // 自动加载
        static $is_loaded = false;
        if($is_loaded) return;
        $is_loaded = true;

        $install = Plugins::count('name="DataSource" and is_installed=1 and is_enabled=1');
        if(!$install) return;

        $sources = DataSources::find(['conditions'=>'status = 1']);
        iterator_apply($sources, function ($iterator){
            $item = $iterator->current();

            # 如果有需要注入的话，则初始化对象并注入
            if($item->injection_name){
                try{
                    $connect = [];
                    switch($item->adapter){
                        case 'sqlite':
                            $connect = ['dbname' => $item->dbname];
                            break;
                        case 'mysql':
                        case 'postgresql':
                            $connect = [
                               'host'     => $item->host ?: 'localhost',
                               'port'     => $item->port ?: '3306',
                               'dbname'   => $item->dbname,
                               'username' => $item->username ?: 'root',
                               'password' => $item->password,
//                               'options'  => [\PDO::ATTR_TIMEOUT=>3], # 3秒超时
                               'charset'  => 'utf8mb4'
                            ];
                            break;
                    }

                    #初始化对象并绑定事件
                    $db = (new DB)->newInstance($item->adapter, $connect);
                    PA::$di->set($item->injection_name, $db);
                    if($item->bind_events_manager) $db->setEventsManager(PA::$em);
                }catch(\Throwable $e){
                    # 如果出错，则更新备注信息，并禁用
                    $item->remark = $e->getMessage(); // 更新配置
                    $item->status = 0; // 不可用
                    $item->update();
                }
            }
            return true;
        },[$sources]);
        # 如果有绑定，则绑定


//        PA::$loader->registerDirs([POWER_DATA.'DataSource'],true);
//        PA::$loader->registerNamespaces([
//            'plugins\DataSource\Models'=>POWER_BASE_DIR.'plugins/DataSource/Models'
//        ],true);

        // 初始化到DI，如果选中的话

        // 加入事件监听，如果选中的话
    }
}