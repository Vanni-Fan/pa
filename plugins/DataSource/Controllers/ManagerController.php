<?php
namespace plugins\DataSource\Controllers;

use HtmlBuilder\Components;
use HtmlBuilder\Element;
use HtmlBuilder\Forms;
use HtmlBuilder\Layouts;
use HtmlBuilder\Parser\AdminLte\Parser;
use plugins\DataSource\Models\DataSources;
use Power\Controllers\AdminBaseController;

class ManagerController extends AdminBaseController
{
    private $params = [];
    public function initialize()
    {
        $this->params = $this->getParam();
        unset($this->params['Rule']);
        return parent::initialize();
    }

    // 列表页面
    function settingsAction()
    {
        $this->title = '数据源管理';
        $this->subtitle = '帮您管理数据库的连接';

        # 如果有命令，就返回相应的命令
        if($this->getParam('command')) return $this->{$this->getParam('command')}();

        # 返回设置页面
        $parser = new Parser();
        $this->view->contents = $parser->parse(
            Components::table('数据源列表')->description('系统数据源不可删除')
                ->fields([
                    ['name'=>'source_id','text'=>'编号'],
                    ['name'=>'name','text'=>'名称'],
                    ['name'=>'adapter','text'=>'类型'],
                    ['name'=>'dbname','text'=>'库名'],
                    ['name'=>'host','text'=>'主机'],
                    ['name'=>'port','text'=>'端口'],
                    ['name'=>'username','text'=>'用户名'],
                    ['name'=>'password','text'=>'密码'],
                    ['name'=>'injection_name','text'=>'注入名'],
                    ['name'=>'prefix','text'=>'表前缀'],
                    ['name'=>'bind_events_manager','text'=>'事件绑定','render'=>'v=>v?"绑定":"未绑定"','filter'=>0],
                    ['name'=>'created_time','text'=>'创建时间','show'=>0],
                    ['name'=>'updated_time','text'=>'更新时间','show'=>0],
                    ['name'=>'created_user','text'=>'创建者','show'=>0,'filter'=>0],
                    ['name'=>'updated_user','text'=>'更新者','show'=>0,'filter'=>0],
                ])
            ->primary('source_id')
            ->queryApi($this->getUrl(['command'=>'getList']))
            ->deleteApi($this->getUrl(['command'=>'delSource','sid'=>'_ID_']))
            ->updateApi($this->getUrl(['command'=>'updateSource','sid'=>'_ID_']))
            ->createApi($this->getUrl(['command'=>'updateSource','sid'=>0]))
            ->canEdit('操作')
            ->editCallback('(s,v)=>s.source_id=="######" ? "配置文件" : v')
        );

        # 渲染
        $parser->setResources($this);
        $this->render(null);
    }

    private function getUrl(array $param, $method='GET'){
        return $this->url($method=='GET'?'display':'update', array_merge($this->params, $param));
    }

    /**
     * 删除
     */
    function delSource(){
        $sid = explode(',',$this->params['sid']);
        DataSources::find(['conditions'=>'source_id IN ({source_id:array})','bind'=>['source_id'=>$sid]])->delete();
        $this->getList();
    }

    /**
     * ajax获取数据
     */
    function getList(){
        $data = DataSources::getSources($_POST['filters']??[]);
        $this->jsonOut([
            'list'  => $data,
            'total' => count($data),
            'page'  => 1,
            'size'  => 10,
        ]);
    }

    /**
     * 创建和更新
     * @throws \Exception
     */
    function update(){
        if($this->params['sid']){
            $obj = DataSources::findFirst($this->params['sid']+0);
        }else{
            $obj = new DataSources;
        }
        $obj->assign($_POST)->save();
        $url = $this->url('update', ['action'=>'set','event'=>'setting','item'=>$this->params['item_id']]);
        $this->response->redirect($url);
    }

    /**
     * 更新页面
     */
    function updateSource(){
        $this->title = '编辑数据源';
        $this->subtitle = '数据源是值数据库的连接对象';
        $parser = new Parser();
        $data = $this->params['sid'] ? DataSources::findFirst($this->params['sid']+0)->toArray() : [];
        $this->view->contents = $parser->parse(
            Forms::form($this->getUrl(['command'=>'update']),'POST')->add(
                Layouts::box( // 消息框
                    // 消息框主体
                    Element::create('div')->style('display: block;')->add(
                        Layouts::columns()->column(
                            Forms::input('name','数据源名称',$data['name']??'')->labelWidth(3)->labelPosition('left-right'),6
                        )->column(
                            Forms::input('injection_name','注入到PA::$di',$data['injection_name']??'')->labelWidth(3)->labelPosition('left-right'),6
                        ),

                        Layouts::columns()->column(
                            Forms::input('username','数据源用户',$data['username']??'')->labelWidth(3)->labelPosition('left-right'),6
                        )->column(
                            Forms::input('password','数据源密码',$data['password']??'')->labelWidth(3)->labelPosition('left-right'),6
                        ),

                        Layouts::columns()->column(
                            Forms::input('host','数据源主机',$data['host']??'')->labelWidth(3)->labelPosition('left-right'),6
                        )->column(
                            Forms::input('port','数据源端口',$data['port']??'','number')->labelWidth(3)->labelPosition('left-right'),6
                        ),

                        Layouts::columns()->column(
                            Forms::input('dbname','数据库名',$data['dbname']??'')->labelWidth(3)->labelPosition('left-right'),6
                        )->column(
                            Forms::input('prefix','表前缀',$data['prefix']??'')->labelWidth(3)->labelPosition('left-right'),6
                        ),

                        Layouts::columns()->column(
                            Forms::checkbox('bind_events_manager','绑定到PA::$em',$data['bind_events_manager']??0)->labelWidth(3)->labelPosition('left-right')->choices([['text'=>'绑定', 'value'=>'1']]),6
                        )->column(
                            Forms::select('adapter','数据源类型',$data['adapter']??'mysql')->labelWidth(3)->labelPosition('left-right')->choices([
                                ['text'=>'mysql','value'=>'mysql'],['text'=>'sqlite','value'=>'sqlite'],['text'=>'postgresql','value'=>'postgresql'],
                            ]),6
                        ),
                    ),
                    '编辑数据源详情',
                    // 消息框尾部
                    Element::create('div')->style('display: flex;justify-content: space-around;')->add(
                        Forms::button('','重置')->action('reset'),
                        Forms::button('','保存')->action('submit')
                    )
                )
            )
        );

        # 渲染
        $parser->setResources($this);
        $this->render(null);

    }
}