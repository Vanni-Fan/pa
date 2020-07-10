<?php
namespace plugins\Tables\Controllers;
use HtmlBuilder\Components;
use HtmlBuilder\Element;
use HtmlBuilder\Forms;
use HtmlBuilder\Layouts;
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
use statistics\Services\ShareStatistic;

class TablesController extends AdminBaseController
{
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

    # 首页
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
            if($field['canShow']) $fields[]  = $field;
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
        $table->updateApi($this->url('update',['item_id'=>'_ID_']));
        $table->deleteApi($this->url('delete',['item_id'=>'_ID_']));


        $parse  = new Parser();
        $table_str  = $parse->parse($table);
        $parse->setResources($this);

//        $this->view->contents = '<pre>'.print_r($this->table,1).print_r($params,1).print_r($this->fields,1).'</pre>'.$table_str;
        $this->view->contents = $table_str;
        $this->render(null);
    }

    # 列表
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

    # 显示详情
    public function newAction(){ $this->displayAction(); }
    public function displayAction(){
        $model = BaseModel::get($this->table['source_id'], $this->table['table']);
        if($this->item_id) {
            $param = [];
            foreach ($this->fields as $index => $field) {
                if ($field['primary']) $param[] = "{$field['field']}=?$index";
            }
            $where[0] = implode(' AND ', $param);
            $where['bind'] = explode('-', $this->item_id);
            $data = $model->findFirst($where);
        }else{
            $data = $model->create();
        }

        $parse = new Parser();
        $fields = Element::create('div')->style('display:flex;flex-wrap:wrap;');
        foreach($this->fields as $field){
            if(!$field['canShow']) continue;
            $fields->add(
                Forms::input($field['field'],$field['text'],htmlentities($data->{$field['field']}??''),$field['type'])
                    ->labelIcon($field['icon']??'')
                    ->tooltip($field['tooltip']??'')
            );
        }
        $box = Layouts::box($fields,'编辑数据',Element::create('div')->style('display:flex;justify-content:space-between;')->add(
            Forms::button('','返回')->on('click','history.back()')->style('default'),
            Forms::button('','提交')->action('submit'),
        ));
        $this->view->contents = $parse->parse(
            Forms::form($this->item_id ? $this->url('update') : $this->url('new'),'POST')->add($box)
        );
        $parse->setResources($this);
        $this->render(null);
    }

    # 创建或更新
    public function appendAction(){ $this->updateAction(); }
    public function updateAction(){
        $model = BaseModel::get($this->table['source_id'], $this->table['table']);
        if($this->item_id){
            $param = [];
            foreach ($this->fields as $index => $field) {
                if ($field['primary']) $param[] = "{$field['field']}=?$index";
            }
            $where[0] = implode(' AND ', $param);
            $where['bind'] = explode('-', $this->item_id);
            $model::findFirst($where)->assign($_POST)->update();
        }else{
            $model->assign($_POST)->create();
        }
        $this->response->redirect($this->url(),true);
    }


    # 删除
    public function deleteAction(){
        $model = BaseModel::get($this->table['source_id'],$this->table['table']);
        $db    = $model->getWriteConnection();
        $sql   = 'DELETE FROM '. $db->escapeIdentifier($model->getSource()).' WHERE ';
        $where = [];
        foreach($this->fields as $field){
            if($field['primary']) $where[] = $db->escapeIdentifier($field['field']) . '=?';
        }
        $sql .= implode(' AND ', $where);

        $items = is_array($this->item_id) ? $this->item_id : [$this->item_id];
        foreach($items as $item){
            $db->execute($sql, strpos($item,'-')===false ? $item : explode('-', $item));
        }
        $this->listAction();
    }
}
