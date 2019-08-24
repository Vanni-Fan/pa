<?php
namespace plugins\Tables\Controllers;
use AdminBaseController;

class TablesController extends AdminBaseController
{
    /**
     * @var \Phalcon\Mvc\Model null
     */
    protected $model = null;
    protected $page_size = 10;
    
    public function initialize()
    {
        parent::initialize();
        $params = $this->dispatcher->getParams();
        $table  = ucfirst($params['table']);
        $this->params = $params;
        $this->title = $table;
        $this->table = $table;
        $this->subtitle = "Management tool for table:<u>$table</u> from plugin:<u>TablesPlugins</u>.";
        $model_dir  = POWER_DATA.'/TablesPlugins/';
        $model_file = $model_dir.$table.'.php';
        $model_class= '\\Tables\\'.$table;
        $db_var = var_export($params['db'],1);
        if(!is_dir($model_dir)) mkdir($model_dir);
        $file_body = '<?php
/** The file is generated automatically by TablePlugin */
namespace Tables;
use Phalcon\Db\Adapter\Pdo\Factory;
class ' . $table . ' extends \Phalcon\Mvc\Model{
    public function initialize(){
        $this->setDi(\PA::$di);
        \PA::$di->set("plugin_table_db", Factory::load(' . $db_var . '));
        $this->setConnectionService("plugin_table_db");
    }
}';
        if(!file_exists($model_file) || filesize($model_file) != strlen($file_body)) file_put_contents($model_file, $file_body);
        require $model_file;
        $this->model = new $model_class();
        $this->fields = array_column($this->model->getWriteConnection()->query('describe '.strtolower($table))->fetchAll(FETCH_ASSOC), null,'Field');
        $this->primary = array_filter($this->fields, function($v){
            return $v['Key'] == 'PRI';
        });
        $this->fixField();
        $this->view->fields = $this->fields;
        $this->view->primary = $this->primary;
        $this->view->table  = $table;
        $this->view->primary= $this->primary;
    
        $this->addJs('/dist/tables.plugins.js');
        $this->addJs('/dist/bower_components/bootstrap/js/modal.js');
        $this->addJs('/dist/bower_components/datatables.net/js/jquery.dataTables.min.js');
        $this->addJs('/dist/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js');
        $this->addCss('/dist/tables.plugins.css');
        $this->addCss('/dist/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css');
    }
    
    public function fixField(){
        foreach($this->fields as &$field){
            $field['name']   = ucwords(str_replace('_',' ', $field['Field']));
            $field['is_pri'] = $field['Key'] == 'PRI';
            $field['null']   = $field['Null'] == 'YES';
            $field['disabled']= $field['Extra'] == 'auto_increment' || $field['Default'] == 'CURRENT_TIMESTAMP';
            if(strpos($field['Type'],'enum(') === 0){
                $field['Default'] = explode("','", substr($field['Type'], 6, -2));
                $field['Type'] = 'select';
            }
        }
    }
    
    public function indexAction(){
        $where  = [];
        $script = '';
        if($this->request->get('field')){
            $fields     = $this->request->get('field');
            $operations = $this->request->get('operations');
            $values     = $this->request->get('value');
            foreach($fields as $i=>$field){
                if(!in_array($field, array_keys($this->fields))) throw new \Exception("Field:$sort not exists!");
                $condition[]     = $field . ' ' . $operations[$i] . " ?$i";
                $where['bind'][] = $values[$i];
                $script .= "addField(!$i, '$field', '{$operations[$i]}', '{$values[$i]}');\n";
            }
            $where['conditions'] = implode(' AND ', $condition);
        }
        if($sort = $this->request->get('sort_field')){
            $method = $this->request->get('sort_method');
            if(!in_array($sort, array_keys($this->fields))) throw new \Exception("Field:$sort not exists!");
            if(!in_array($method, ['asc','desc'])) throw new \Exception("Sore method must be asc or desc");
            $where['order'] = "$sort $method";
        }
        $count = $this->model->find($where)->count();
        $cur_page  = $this->getParam('page') ?? 1;
        $page_size = $this->page_size;
        $where['offset'] = ($cur_page-1) * $page_size;
        $where['limit']  = $this->page_size;
        $this->addScript($script);
        $this->view->conditions = ['>','<','=','>=','<=','!=','in','like'];
        $this->view->data       = $this->model->find($where);
        $this->view->where      = $where;
        $this->view->page       = $this->getPaginatorString($count, $cur_page, $this->page_size,5);
        $this->render();
    }
    
    public function displayAction(){
        $where = [
            array_key_first($this->primary) . ' = ?0',
            'bind' => [$this->item_id]
        ];
        $this->view->default = $this->model->findFirst($where)->toArray();
        $this->render('tables/edit');
    }
    
    public function updateAction(){
        $where = [
            array_key_first($this->primary) . ' = ?0',
            'bind' => [$this->item_id]
        ];
        $data = $this->model->findFirst($where);
        
        $data->assign($this->request->get());
        $data->save();
        $this->response->redirect($this->url(),true);
    }
    
    public function newAction(){
        $this->render('tables/edit');
    }
    
    public function appendAction(){
        $this->model->create($this->request->get());
        $this->response->redirect($this->url(),true);
    }
    
    public function deleteAction(){
        $where = [
            array_key_first($this->primary) . ' = ?0',
            'bind' => [$this->item_id]
        ];
        $this->model->findFirst($where)->delete();
        $this->jsonOut(['msg'=>'ok']);
    }
}