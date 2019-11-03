<?php
namespace Power\Controllers;

use HtmlBuilder\Components;
use HtmlBuilder\Parser\AdminLte\Parser;
use Power\Models\Logs;

class LogsController extends AdminBaseController {
    protected $title = '日志管理';
    public function indexAction(){
        $parser = new Parser();
        $parser->style(/** @lang CSS */'
            .json-code{
                max-width:400px;
                overflow:hidden;
                text-overflow:ellipsis;
                white-space:nowrap;
            }
        ');
        $parser->script(/** @lang JavaScript */<<<'Out'
            function showCode(e){
                console.log(e);
                showDialogs({
                    body:'<pre style="max-height:400px;">' + JSON.stringify(JSON.parse(e.currentTarget.innerText),'',4) + '</pre>',
                    close:{text:"关闭",click:function(_){_.close()}}
                })
            }
Out
            );
        $this->view->contents = $parser->parse(
            Components::table('日志列表')->fields(
                [
                    ['name'=>'log_id', 'text'=>'ID','sort'=>1, 'filter'=>1,'class'=>'text-center'],
                    ['name'=>'menu_id', 'text'=>'菜单ID','sort'=>1, 'show'=>0, 'filter'=>1],
                    ['name'=>'menu_name', 'text'=>'菜单名','sort'=>0, 'show'=>1, 'filter'=>0],
                    ['name'=>'user_id', 'text'=>'用户ID','sort'=>1, 'show'=>0, 'filter'=>0],
                    ['name'=>'user_name', 'text'=>'用户名','sort'=>0, 'filter'=>0,'class'=>'text-center'],
                    ['name'=>'url', 'text'=>'请求地址','sort'=>1, 'filter'=>1],
                    ['name'=>'request', 'text'=>'请求体', 'filter'=>1,'show'=>1, 'width'=>400,'class'=>'json-code','render'=>'i=>"<span onclick=\'showCode(event)\'>"+i+"</span>"'],
                    ['name'=>'server', 'text'=>'服务变量', 'filter'=>1,'show'=>1, 'width'=>400,'class'=>'json-code','render'=>'i=>"<span onclick=\'showCode(event)\'>"+i+"</span>"'],
                    ['name'=>'created_time', 'text'=>'发生时间','sort'=>1, 'filter'=>1],
                ]
            )->queryApi($this->url('list'))
        );
        $parser->setResources($this);
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

        if(isset($_POST['sort'])){
            $where['order'] = '';
            foreach($_POST['sort'] as $sort){
                $where['order'] .= $sort['name'].' '.$sort['type'].',';
            }
            $where['order'] = substr($where['order'],0,-1);
        }

        # 条件
        if(isset($_POST['filters'])) Logs::parseWhere(['where'=>$_POST['filters']],$where);

        $data = [];
        foreach(Logs::find($where) as $row){
            $d = $row->toArray();
            $d['menu_name']    = $row->Menu ? $row->Menu->name : '';
            $d['user_name']    = $row->User ? $row->User->nickname : '';
            $data[] = $d;
        }

        $this->jsonOut(
            [
                'list'=>$data,
                'total'=>Logs::count(['conditions'=>$where['conditions'],'bind'=>$where['bind']]),
                'page'=>(int)$page,
                'size'=>(int)$size,
                'query'=>[
                    'filter'=>$_POST['filters']??[],
                    'limit'=>$_POST['limit']??['page'=>$page, 'size'=>$size]
                ]
            ]
        );
    }
}