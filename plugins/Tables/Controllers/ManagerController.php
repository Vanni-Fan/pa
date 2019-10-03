<?php
namespace plugins\Tables\Controllers;
use HtmlBuilder\Components;
use HtmlBuilder\Components\Table;
use HtmlBuilder\Element;
use HtmlBuilder\Forms;
use HtmlBuilder\Layouts;
use HtmlBuilder\Parser\AdminLte\Parser;
use PDO;
use Power\Controllers\AdminBaseController;
use PA;

class ManagerController extends AdminBaseController
{
    public function settingsAction(){
        $this->title = 'Tables插件设置';
        if($this->getParam('command')){
             return $this->{$this->getParam('command')}();
        }
        
        $parser = new Parser();
        $this->view->content = $parser->parse(
            Components::table('数据源列表')
                      ->queryApi($this->url('display',['action'=>'set','event'=>'setting','command'=>'getSource']))
                      ->createApi($this->url('display',['action'=>'set','event'=>'setting','command'=>'edit']))
                      ->fields(
                          [
                              ['name'=>'id','text'=>'#'],
                              ['name'=>'name','text'=>'名称','sort'=>1,'filter'=>1],
                              ['name'=>'type','text'=>'类型','sort'=>1,'filter'=>1],
                              ['name'=>'host','text'=>'主机','sort'=>1,'filter'=>1],
                              ['name'=>'port','text'=>'端口','sort'=>1,'filter'=>1],
                              ['name'=>'user','text'=>'用户','sort'=>1,'filter'=>1],
                              ['name'=>'password','text'=>'密码','sort'=>1,'filter'=>1],
                          ]
                      )->primary('id')
            ,
            Components::table('菜单列表')
                      ->queryApi($this->url('display',['action'=>'set','event'=>'setting','command'=>'getMenu']))
                      ->fields(
                            [
                                ['name'=>'id','text'=>'#'],
                                ['name'=>'rule_id','text'=>'菜单ID'],
                                ['name'=>'source_id','text'=>'数据源'],
                                ['name'=>'table_name','text'=>'表名'],
                                
                            ]
                      )
        );
        $parser->setResources($this);
        $this->render();
    }
    public function getSource(){
        $where = [];
        $operations = [];
        if($_POST['filters']){
            foreach($_POST['filters']??[] as $filter){
                $where .= $filter['name'] . $filter['operation'] . ' :'.$filter['name'];
                $params[$filter['name']] = $filter['value'];
            }
        }
        print_r($_POST);
        $data = PA::$db->query('select * from plugins_table_sources');
        $this->jsonOut(
            [
               'list'=>$data->fetchAll(PDO::FETCH_ASSOC),
               'total'=>1000,
               'page'=>5,
               'size'=>50,
            ]
        );
    }
    public function getMenu(){
        $data = PA::$db->query('select * from plugins_table_menus');
        $this->jsonOut(
            [
               'list'=>$data->fetchAll(PDO::FETCH_ASSOC),
               'total'=>1000,
               'page'=>5,
               'size'=>50,
           ]
        );
    }
    public function edit(){
        $parser = new Parser();
        $this->view->content = $parser->parse(
            
            Forms::form('','')->add(
                Layouts::columns()->column(
                    Element::create('div')->add(
                        Forms::input('host','名称')->labelWidth(4)->labelPosition('left-right'),
                        Forms::select('类型','type',['AA','BB'],'single')->labelWidth(4)->labelPosition('left-right'),
                        Forms::input('host','名称')->labelWidth(4)->labelPosition('left-right')
                    )
                    ,6
                )->column(
                    Forms::input('port','端口')->labelWidth(4)->labelPosition('left-right'),6
                ),
                Forms::button('返回'),
                Forms::button('提交')
            )
        );
        $parser->setResources($this);
        $this->render('manager/source-edit');
    }
}