<?php
namespace Power\Controllers;

use HtmlBuilder\Components;
use HtmlBuilder\Element;
use HtmlBuilder\Forms;
use HtmlBuilder\Parser\AdminLte\Parser;

class IndexController extends AdminBaseController{
    public function indexAction(){
        $this->render(null);
        return;
//        phpinfo();
//        exit;
        $this->title = 'HtmlBuilder Test Page';
        $parser = new Parser();
        $inputs = [];

//        // 输入框实例
//        $inputs[] = $parser->parse(
//            Forms::input('c','4个数字8验证器')->statistics()->inputBeforeIcon('fa fa-users')->validate(
//                Validate::number('请输入4位数字',1,8888),
//                Validate::regex('必须全是8','^8+$'),
//            )
//        );
//        // 日期测试
//        $inputs[] = $parser->parse(Forms::input('a','日期和时间')->statistics()->subtype('datetime'));
//        $inputs[] = $parser->parse(Forms::input('a','邮件')->statistics()->subtype('email'));
//        // 分栏样式
//        $inputs[] = $parser->parse(Forms::input('b','个人说明')->required()->placeHolder('填写你的说明')->description('xxxx')->labelWidth(3)->tooltip('hhhh'));
//        // 电话
//        $inputs[] = $parser->parse(Forms::input('c','电话测试')->inputBeforeIcon('fa fa-users')->required()->inputMask("'mask':'(999) 999-9999'"));
//        $inputs[] = $parser->parse(Forms::input('c','日期')->inputBeforeIcon('fa fa-star')->required()->subtype('date'));
//        $inputs[] = $parser->parse(Forms::input('c','时间')->inputBeforeIcon('fa fa-users')->required()->subtype('time'));
//        $inputs[] = $parser->parse(Forms::input('c','隐藏的')->labelWidth(2)->labelPosition('right-right')->visible(false));
//        $inputs[] = $parser->parse(Forms::input('c','标签在右边')->labelWidth(3)->labelPosition('right-left'));
//        $inputs[] = $parser->parse(Forms::input('c','标签在左边，并且居右')->labelWidth(4)->labelPosition('left-right'));
//        $inputs[] = $parser->parse(Forms::input('c','禁用状态')->labelWidth(5)->labelPosition('left-left')->enabled(false));
//        $inputs[] = $parser->parse(Forms::input('c','标签在左左')->inputBeforeIcon('fa fa-users')->labelWidth(5)->labelPosition('left-left')->inputAfterIcon('fa fa-users'));
//
//        //列测试
//        $inputs[] = $parser->parse(Layouts::columns()->column(Forms::input('a')->value('我占4列'),4)->column(Forms::input('a')->placeHolder('我占8列'),8));
//
//        //盒子测试
//        $ttt = $parser->parse(Layouts::columns()->column(Forms::input('a','用户名')->labelWidth(3),4)->column(Forms::input('a'),8));
//        $inputs[] = $parser->parse(Layouts::box($ttt,'标题',Forms::input('a','模块名称')->labelWidth(4))->style('Success')->labelIcon('fa fa-users')->canClose()->canMini()->class('ttt'));
//
//        //tag测试
//        $inputs[] = $parser->parse(
//            Layouts::columns()->column(
//                Layouts::tabs()->tab('PageA', Forms::input('a','ahhh'))->tab('PageB', Forms::input('b','tttt'),true),
//                3
//            )->column(
//                Forms::input('cccc','kkkk'),
//                9
//            )
//        );
//
//        // 按钮组
//        $inputs[] = $parser->parse(
//            Forms::button('name')->subtype('group')->add(
//                Forms::button('aaa')->subtype('default')->label('AAA')->on('click','alert("aaa");'),
//                Forms::button('bbb')->subtype('default')->label('BBB')->style('default')->on('click','alert("bbb");'),
//                Forms::button('ccc')->subtype('default')->label('CCC')->style('danger')->on('click','alert("ccc");'),
//            )
//        );
//
//        // 输入按钮
//        $inputs[] = $parser->parse(
//            Forms::button('')
//                ->btnBeforeIcon('fa fa-users')
//                ->btnAfterIcon(Forms::button('def')->subtype('default')->label('搜索')->on('click','alert("点搜索")')
//            )->subtype('input')->on('click','alert("点击事件")')
//        );
//
//        $inputs[] = $parser->parse(
//            Forms::checkbox('asdfsdfa','bb','1')->choices([['text'=>'aaa','value'=>'1'],['text'=>'bbb','value'=>'2']])->other(
//               '其他选项'
//            )
//        );
//        $inputs[] = $parser->parse(
//            Forms::radio('asdfsdfa','bbx','2')->choices([['text'=>'aaa','value'=>'1'],['text'=>'bbb','value'=>'2']])->other(
//                Forms::input('xxx')->placeHolder('其他')->disabled()
//            )
//        );
//
        // 普通下来选择
        $inputs[] = $parser->parse(
            Forms::select('se1','普通选择','11')->choices([['text'=>'aaa','value'=>'1'],['text'=>'bbb','value'=>'2']]),
            Forms::select('se2','普通带搜索','11','select2')->choices([['text'=>'aaa','value'=>'1'],['text'=>'bbb','value'=>'2']]),
            Forms::select('se2','可以输入不存在的值','11','select2')->isTags(1)->choices([['text'=>'aaa','value'=>'1'],['text'=>'bbb','value'=>'2']]),
            Forms::select('se2','可以输入多个值','11','select2')->multiple(1)->isTags(1)->choices([['text'=>'aaa','value'=>'1'],['text'=>'bbb','value'=>'2']]),
        );

        // 3级联动
        $inputs[] = $parser->parse(
            Element::create('div')->id('mse')->style("#mse>div{display:flex}\n#mse button{height:35px;}")->add(
                Forms::button('')->subtype('group')
                ->add(
                    Forms::button('')->class('fa fa-users'),
                    Components::multiselect('')->on('change','alert(2)'," select[name='sub_category']")->id('vanni')->addSelect(
                        'main_category','','1',$this->url('display',['command'=>'getCategory'])
                    )->addSelect(
                        'abc_category','','1',$this->url('display',['command'=>'getCategory','main_id'=>'[$0]'])
                    )->addSelect(
                        'sub_category','','1',$this->url('display',['command'=>'getCategory','main_id'=>'[$0]','sub_id'=>'[$1]'])
                    ),

                    Forms::button('')->class('fa fa-search')
                )
            )
        );

        $inputs[] = $parser->parse(
            Forms::form($this->url('update'))->add(
//                Forms::input('aaa','用户')->subtype('color'),
//                Forms::input('bbb','秘密')->subtype('password'),
                Forms::file('xxxx')->accept('image/*')->placeHolder('用户头像')->setCorpSize(200,400),//,//->label('用户头像')->labelWidth(4)->description('头像必须300x234'),
//                Forms::file('yyyy[]')->accept('image/*')->subtype('multiple')->setCorpSize(200,200),//->label('用户头像')->labelWidth(4)->description('头像必须300x234'),
                Forms::button('','重置')->action('reset'),
                Forms::button('','提交')->action('submit'),
            )
        );

        $inputs[] = $parser->parse(
//            Forms::form($this->url('update'))->add(
                Components::table('浏览器分布情况')->query(
                    [
                        'filters'=>[
                            'op'=>'AND',
                            'sub'=>[
                                ['key'=>'o.user_id','op'=>'>=','val'=>'11'],
                                ['key'=>'b','op'=>'<','val'=>'1001'],
                                ['key'=>'d.sdf','op'=>'>=','val'=>'11'],
                                ['key'=>'xx.sdf','op'=>'>=','val'=>'11'],
                                ['key'=>'c','op'=>'>=','val'=>'11'],
                            ]
                        ],
                        'sort' => [
                            ['name'=>'a','type'=>'desc'],
                            ['name'=>'b','type'=>'asc']
                        ],
                        'limit'=>[ // start, size
                            'page'=>10,
                            'size'=>50
                        ]
                    ]
                )->queryApi(
                    $this->url('update',['action'=>'getTableData'])
                )->fields(
                    [
                        ['name'=>'a', 'text'=>'字段A','tooltip'=>'这个是字段A','sort'=>1, 'filter'=>'o.user_id', 'edit'=>1, 'delete'=>'canDelete', 'width'=>200, 'show'=>1, 'type'=>'date','params'=>[], 'icon'=>'fa fa-users', 'class'=>''],
                        ['name'=>'b', 'text'=>'字段B','tooltip'=>'这个是字段B','sort'=>1, 'filter'=>1, 'edit'=>1, 'delete'=>'canDelete', 'width'=>200, 'show'=>1, 'type'=>'time','params'=>[], 'icon'=>'fa fa-users', 'class'=>''],
                        ['name'=>'c', 'text'=>'字段C','tooltip'=>'这个是字段C','sort'=>'aa.c_Id', 'filter'=>1, 'edit'=>1, 'delete'=>'canDelete', 'width'=>200, 'show'=>1, 'type'=>'color','params'=>[], 'icon'=>'fa fa-users', 'class'=>''],
                        ['name'=>'d', 'text'=>'字段D','tooltip'=>'这个是字段D','sort'=>1, 'filter'=>1, 'edit'=>1, 'delete'=>'canDelete', 'width'=>200, 'show'=>1, 'type'=>'datetime-local','params'=>[], 'icon'=>'fa fa-users', 'class'=>''],
                        ['name'=>'e', 'text'=>'字段E','tooltip'=>'这个是字段E','sort'=>1, 'filter'=>1, 'edit'=>1, 'delete'=>'canDelete', 'width'=>200, 'show'=>1, 'type'=>'datetime','params'=>[], 'icon'=>'fa fa-users', 'class'=>''],
                    ]
                )->primary('c')->deleteApi(
                    $this->url('delete',['item_id'=>'{id}'])
                )->description('测试表格组件')//->afterQueryCallback('t=>alert("查询之后")')->beforeQueryCallback('t=>alert("查询之前")'),
//                Components::timerange('time')->label('时间段')->value(['12:12:12','13:13:13']),
//                Components::multiselect($this->url('index',['action'=>'getItems']))
//                  ->addSelect('a[]',null,2,$this->url('index',['action'=>'getItems']).'?type=A')
//                  ->addSelect('a[]',null,1,$this->url('index',['action'=>'getItems']).'?type=B')
//                  ->addSelect('c',null,1,$this->url('index',['action'=>'getItems']).'?type=C')
//                  ->addSelect('d',null,1,$this->url('index',['action'=>'getItems']).'?type=D')
//                  ->addSelect('e',null,1,$this->url('index',['action'=>'getItems']).'?type=E')
//                  ->addSelect('f',null,1)->label('选择地区'),
//                Forms::button('提交')->action('submit')
//            )
        );

        // 为什么在这里就可以
//        $p2 = new Parser();
//        $this->view->setting_items = [
//            0 => $p2->parse(
//                Forms::file('aaa'),
//                Forms::file('bbb'),
//            )
//        ];
//        $p2->setResources($this);

//            Forms::textarea()->subtype('wysihtml5'),
//            Forms::textarea()->subtype('simple')->label('xxxxx')->labelWidth(3),
//            Forms::textarea()->subtype('ckeditor'),
//        );

//print_r($inputs);
//exit;
    
        $parser->setResources($this);
        $this->view->inputs = $inputs;

//        $a = //new Parser(
//            Layouts::columns(2)->add(
//                Layouts::box('右边搜索栏')->add(
//                    Forms::form('search')->add(
//                        Forms::input('text'),
//                        Forms::button('Search')
//                    ),
//                ),
//                Layouts::box('左边注册栏')->add(
//                    Forms::form('register')->add(
//                        Forms::input('username'),
//                        Components::datetime('expire_data'),
//                        Forms::button('Register','s')->attr('sdf','')->enabled()->value('ss')
//                            ->labelPosition('')
//                            ->label(''),
//                        Forms::file(),
//                    )
//                )
////            )
//        );
//        var_dump(get_class($this->view));
        $this->render();
    }
    public function displayAction(){
        $param = $this->getParam();
        if(empty($param['main_id'])){
            $rs = [
                ['text'=>'AAA','value'=>1],['text'=>'BBB','value'=>2],['text'=>'CCC','value'=>3],
            ];
        }elseif(empty($param['sub_id'])){
            $rs = [
                1=>[
                    ['text'=>'AAA-111','value'=>1],['text'=>'AAA-222','value'=>2],['text'=>'AAA-333','value'=>3]
                ],
                2=>[
                    ['text'=>'BBB-111','value'=>1],['text'=>'BBB-222','value'=>2],['text'=>'BBB-333','value'=>3]
                ],
                3=>[
                    ['text'=>'CCC-111','value'=>1],['text'=>'CCC-222','value'=>2],['text'=>'CCC-333','value'=>3]
                ]
            ][$param['main_id']??1];
        }else{
            $rs = [
                1=>[
                    ['text'=>'AAA-111-111','value'=>1],['text'=>'AAA-222-222','value'=>2],['text'=>'AAA-333-333','value'=>3]
                ],
                2=>[
                    ['text'=>'BBB-111-111','value'=>1],['text'=>'BBB-222-222','value'=>2],['text'=>'BBB-333-333','value'=>3]
                ],
                3=>[
                    ['text'=>'CCC-111-111','value'=>1],['text'=>'CCC-222-222','value'=>2],['text'=>'CCC-333-333','value'=>3]
                ]
            ][$param['sub_id']];
        }

        $this->jsonOut($rs);
    }

    public function updateAction(){
        print_r($_POST);
        print_r($_FILES);
        foreach($_FILES as $file){
            if(!is_array($file['name'])){
                foreach($file as $k=>$v){
                    $file[$k] = [$v];
                }
            }
            $count = count($file['name']);
            for($i=0; $i<$count; $i++){
                move_uploaded_file($file['tmp_name'][$i],'d:/tmp/'.$file['name'][$i]);
            }
        }
    }
    
    public function getItemsAction(){
//        $type = $this->getParam('type');
        $type = $_REQUEST['type'] ?? 0;
        $this->jsonOut(
            [
                ['text'=>uniqid().'=>'.$type, 'value'=>random_int(100, 200)],
                ['text'=>uniqid().'=>'.$type, 'value'=>random_int(100, 200)],
                ['text'=>uniqid().'=>'.$type, 'value'=>random_int(100, 200)],
                ['text'=>uniqid().'=>'.$type, 'value'=>random_int(100, 200)],
                ['text'=>uniqid().'=>'.$type, 'value'=>random_int(100, 200)],
            ]
        );
    }
    public function deleteAction(){
        return $this->getTableDataAction();
    }
    public function getTableDataAction(){
        $data = [];
        for($i=0; $i<10; $i++){
            $data[] = [
                'a'=>random_int(100,999),
                'b'=>random_int(100,999),
                'c'=>random_int(100,999),
                'd'=>random_int(100,999),
                'e'=>random_int(100,999),
            ];
        }
        $this->jsonOut(
            [
                'list'=>$data,
                'total'=>1000,
                'page'=>5,
                'size'=>50,
            ]
        );
    }
}
