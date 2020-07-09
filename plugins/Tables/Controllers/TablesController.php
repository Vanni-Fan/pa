<?php
namespace plugins\Tables\Controllers;
use HtmlBuilder\Components;
use HtmlBuilder\Parser\AdminLte\Parser;
use Phalcon\Mvc\Model;
use Phalcon\Text;
use plugins\DataSource\Models\AnyTableModel;
use plugins\DataSource\Models\DataSources;
use plugins\DataSource\Models\ModelInfo;
use plugins\Tables\Models\BaseModel;
use plugins\Tables\Models\TablesFields;
use plugins\Tables\Models\TablesMenus;
use Power\Controllers\AdminBaseController;
use Power\Models\Users;

class abc extends Model {
    public function initialize(){
        $this->setSource('pa_configs');
    }
};
class TablesController extends AdminBaseController
{
    /**
     * @var \Phalcon\Mvc\Model null
     */
    protected $model = null;
    protected $page_size = 10;
    private array $table = [];
    private array $fields = [];
    
    public function initialize()
    {
        parent::initialize();
        $this->table    = TablesMenus::findFirst($this->getParam()['table_id'])->toArray();
        $this->title    = $this->table['menu_name'];
        $this->fields   = TablesFields::find(['table_id=?0','bind'=>[$this->table['id']]])->toArray();
        $this->subtitle = '表格管理插件:<u>TablesPlugins</u>.';
    }
    
    public function indexAction(){
        $params = $this->getParam();
        $fields = [];
        $primary = [];
        # 过滤掉，不能显示的字段
        foreach($this->fields as $field){
            $field['name']   = $field['field'];
            $field['sort']   = intval($field['sort']);
            $field['show']   = intval($field['show']);
            $field['filter'] = intval($field['filter']);
            if($field['canShow']) $fields[] = $field;
            if($field['primary']) $primary[] = $field['field'];
        }
        # 表格对象
        $table = Components::table($this->table['title'])->fields($fields)->primary($primary);
        $table->selectMode($this->table['canSelect'] ? 'multi' : null);
        $table->canMin($this->table['canMin']);
        $table->canClose($this->table['canClose']);
        $table->canEdit($this->table['canEdit'] ? '编辑' : null);
        $table->canAppend($this->table['canAppend']);
        $table->canDelete($this->table['canDelete']);
        $table->canFilter($this->table['canFilter']);
        $table->createApi($this->url('append'));
        $table->queryApi($this->url('list'));
        $table->updateApi($this->url('update'));
        $table->deleteApi($this->url('delete'));


        $parse  = new Parser();
        $table_str  = $parse->parse($table);
        $parse->setResources($this);

//        $this->view->contents = '<pre>'.print_r($this->table,1).print_r($params,1).print_r($this->fields,1).'</pre>'.$table_str;
        $this->view->contents = $table_str;
        $this->render(null);
    }
    public function listAction(){
        $size  = $_POST['limit']['size']??$this->page_size;
        $page  = $_POST['limit']['page']??1;
        $where = [
            'conditions' => '',
            'limit'      => $size,
            'offset'     => ($page - 1) * $size,
            'bind'       => []
        ];
    
        # 条件
        $model = BaseModel::get($this->table['source_id'],$this->table['table']);
        if(isset($_POST['filters'])){
            call_user_func_array([$model,'parseWhere'],[['where'=>&$_POST['filters']], &$where]);
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
        $data = $model::find($where);

        $this->jsonOut(
            [
               'list'=>$data,
               'total'=>$model->count(['conditions'=>$where['conditions'],'bind'=>$where['bind']]),
               'page'=>$page,
               'size'=>$size,
               'query'=>[
                   'filter'=>$_POST['filters']??[],
                   'limit'=>$_POST['limit']??['page'=>$page, 'size'=>$size]
               ]
            ]
        );
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
        $this->model->assign($this->request->get())->create();
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
