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
use plugins\Tables\Models\BaseModel;
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
        $this->params = $this->getParam();
        unset($this->params['Rule']);
        $this->template_path = POWER_BASE_DIR . 'plugins/Tables/views/templates/';
        return parent::initialize();
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
        # 根据不同的子命令来跳转不同的方法
        if($this->getParam('command')) return $this->{$this->getParam('command')}();

        $this->title = 'Tables插件设置';

        # 所有的数据源
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
        # 根据数据源获得菜单的连接
        $this->view->get_menus_url    = $this->getUrl(['command'=>'getMenuList','s_id'=>'S_ID','t_id'=>'T_ID']);
        # 添加具体菜单的连接
        $this->view->add_menus_url    = $this->getUrl(['command'=>'addMenu','s_id'=>'S_ID','t_id'=>'T_ID']);
        # 保存字段的连接
        $this->view->save_fields_url  = $this->getUrl(['command'=>'saveFields','s_id'=>'S_ID','t_id'=>'T_ID']);
        # 保存查询的连接
        $this->view->save_filters_url = $this->getUrl(['command'=>'saveFilters','s_id'=>'S_ID','t_id'=>'T_ID']);
        # 删除菜单的连接
        $this->view->del_menus_url    = $this->getUrl(['command'=>'delMenu','s_id'=>'S_ID','t_id'=>'T_ID']);
        # 所有的图标
        $this->view->all_icons = AdminHelper::getIcons();
        # 所有可选择的菜单
        $this->view->all_menus = Menus::getFlatMenus();
        $this->render('manager/edit');
    }

    /**
     * 删除：菜单
     */
    public function delMenu(){
        $param = $this->getParam();
        $db = (new Users)->getWriteConnection();
        $db->begin();
        try{
            # 查询菜单
            $old = TablesMenus::findFirst(['source_id=?0 and table=?1','bind'=>[$param['s_id'],$param['t_id']]]);
            $id = $old->id;

            # 删除字段
            TablesFields::find(['table_id=?0','bind'=>[$id]])->delete();

            # 删除菜单
            Menus::deleteRule($old->menu_id);
            $old->delete();

            # 删除文件
            BaseModel::del($param['s_id'],$param['t_id']);

            $db->commit();
            echo "删除成功！";
        }catch (\Throwable $e){
            echo $e->getTraceAsString();
            echo "删除失败：".$e->getMessage();
            $db->rollback();
        }
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
                    $menu->icon = $data['menu_icon'];
                    $menu->save();
                }
            } else {  // 添加
                $obj = new TablesMenus();
                $menu = new Menus;
                $menu->assign([
                    'name' => $data['menu_name'],
                    'router' => json_encode(["namespace"=> "plugins\\Tables\\Controllers","controller"=>"Tables"]),
                    'icon' => $data['menu_icon'],
                    'parent_id' => $data['parent_menu_id'] ?: null,
                    'is_enabled' => 1,
                    'created_user' => $this->userinfo['user_id'],
                    'updated_user' => $this->userinfo['user_id'],
                    'created_time' => $time,
                    'updated_time' => $time,
                ])->save();
                $new_menu_id = $menu->menu_id;
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
            $data['filters']   = $data['filters'] ?? '[]';
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

            if(isset($new_menu_id)){
                Menus::findFirst($new_menu_id)->assign(['params'=>json_encode(['table_id'=>$data['id']])])->save();
            }
            # 创建默认字段
            if(empty($_POST['id'])) {
                BaseModel::add($data['source_id'], $data['table']);
                $model = BaseModel::get($data['source_id'], $data['table']);
                $fields = $model->getModelsMetaData()->getDataTypes($model);
                $primary = $model->getModelsMetaData()->getPrimaryKeyAttributes($model);

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
                    $row['text'] = $row['field'] = $field;
                    $row['primary'] = in_array($row['field'], $primary) ? 1 : 0;

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

                    $tf = new TablesFields;
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
    public function saveFields(){
        $fields = json_decode($_POST['fields'],1);
        $db   = (new Users)->getWriteConnection();
        $db->begin();
        try{
            # 删除原先的数据
            foreach($fields as $row){
                # 数据类型修复
                foreach(['sort','filter','show','primary'] as $bool_field){
                    $row[$bool_field] = in_array($row[$bool_field],['1',true]) ? 1 : 0;
                }
                TablesFields::findFirst($row['id'])->assign($row)->update();
            }
            $db->commit();
            echo '菜单保存完成！';
        }catch(\Throwable $e){
            echo '菜单保存失败：'.$e->getMessage();
            $db->rollback();
        }
    }

    /**
     * 编辑：保存查询参数
     */
    public function saveFilters(){
        $params = $this->getParam();
        $db   = (new Users)->getWriteConnection();
        $db->begin();
        try{
            # 删除原先的数据
            $menus = TablesMenus::findFirst(['source_id=?0 and table=?1','bind'=>[$params['s_id'],$params['t_id']]]);
            $menus->filters = json_encode($_POST['filters']);
            $menus->update();
            $db->commit();
            echo '查询条件保存完成！';
        }catch(\Throwable $e){
            echo '查询条件保存失败：'.$e->getMessage();
            $db->rollback();
        }
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
}
