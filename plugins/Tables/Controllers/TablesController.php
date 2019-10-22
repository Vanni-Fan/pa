<?php
namespace plugins\Tables\Controllers;
use HtmlBuilder\Components;
use HtmlBuilder\Parser\AdminLte\Parser;
use Phalcon\Text;
use Power\Controllers\AdminBaseController;

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
        $this->title = '数据表操作';
        $this->subtitle = 'Management tool for table:<u>$table</u> from plugin:<u>TablesPlugins</u>.';
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
    
    private function getModel(){
        $params = $this->getParam();
        $fields = 'Tables\\mp\\'.Text::camelize($params['Rule']['params']['table']);
        $object = call_user_func([$fields, 'getInstance']);
        return $object;
    }
    
    public function indexAction(){
        $parse  = new Parser();
        $params = $this->getParam();
        $fields = 'Tables\\mp\\'.Text::camelize($params['Rule']['params']['table']);
        $object = call_user_func([$fields, 'getInstance']);
        $fields = [];
        foreach($object->describe() as $field){
            $fields[] = [
                'name'=>$field->getName(),
                'text'=>$field->getName(),
                'type'=>$field->getType(),
                'sort'=>1
            ];
        }
        
        $this->view->contents = $parse->parse(
            Components::table($params['Rule']['name'].'的数据')
                      ->fields($fields)
                      ->canEdit(true)
                      ->createApi($this->url('append'))
                      ->updateApi($this->url('update',['item_id'=>'{ID}']))
                      ->queryApi($this->url('list',['ajax'=>'getList']))
        );
        $parse->setResources($this);
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
        $model = $this->getModel();
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
    
        $data = $model->find($where);

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