<?php
namespace Power\Controllers;

use HtmlBuilder\Components;
use HtmlBuilder\Element;
use HtmlBuilder\Forms;
use HtmlBuilder\Layouts;
use HtmlBuilder\Parser\AdminLte\Parser;
use HtmlBuilder\Validate;
use http\Client\Response;

class IndexController extends AdminBaseController{
    public function indexAction(){
//        phpinfo();
//        exit;
        $this->title = 'HtmlBuilder Test Page';
        $parser = new Parser();
        $inputs = [];
//        $inputs[] = $parser->parse(
//            Forms::input('c','手机号有验证')->statistics()->inputBeforeIcon('fa fa-users')->validate(
//                Validate::number('请输入数字',1,8888),
//                Validate::regex('必须全是8','^8+$'),
//            )
//        );
//        $inputs[] = $parser->parse(Forms::input('a','用户名')->statistics()->subtype('datetime'));
//        $inputs[] = $parser->parse(Forms::input('a','用户名')->statistics()->subtype('email'));
//        $inputs[] = $parser->parse(Forms::input('b','个人说明')->required()->placeHolder('填写你的说明')->description('xxxx')->labelWidth(3)->tooltip('hhhh'));
//        $inputs[] = $parser->parse(Forms::input('c','手机号')->inputBeforeIcon('fa fa-users')->required()->inputMask("'mask':'(999) 999-9999'"));
//        $inputs[] = $parser->parse(Forms::input('c','手机号')->inputBeforeIcon('fa fa-star')->required()->subtype('date'));
//        $inputs[] = $parser->parse(Forms::input('c','手机号')->inputBeforeIcon('fa fa-users')->required()->subtype('time'));
//        $inputs[] = $parser->parse(Forms::input('c','ABCD')->labelWidth(2)->labelPosition('right-right')->visible(false));
//        $inputs[] = $parser->parse(Forms::input('c','xxx')->labelWidth(3)->labelPosition('right-left'));
//        $inputs[] = $parser->parse(Forms::input('c','xxx')->labelWidth(4)->labelPosition('left-right'));
//        $inputs[] = $parser->parse(Forms::input('c','xxx')->labelWidth(5)->labelPosition('left-left')->enabled(false));
//        $inputs[] = $parser->parse(Forms::input('c','xxx')->inputBeforeIcon('fa fa-users')->labelWidth(5)->labelPosition('left-left')->inputAfterIcon('fa fa-users'));
//        $inputs[] = $parser->parse(Layouts::columns()->column(Forms::input('a'),4)->column(Forms::input('a'),8));
//        $ttt = $parser->parse(Layouts::columns()->column(Forms::input('a','用户名')->labelWidth(3),4)->column(Forms::input('a'),8));
//        $inputs[] = $parser->parse(Layouts::box($ttt,'标题',Forms::input('a','模块名称')->labelWidth(4))->style('Success')->labelIcon('fa fa-users')->canClose()->canMini()->class('ttt'));
//        $inputs[] = $parser->parse(
//            Layouts::columns()->column(
//                Layouts::tabs()->tab('PageA', Forms::input('a','ahhh'))->tab('PageB', Forms::input('b','tttt'),true),
//                3
//            )->column(
//                Forms::input('cccc','kkkk'),
//                9
//            )
//        );
//        $inputs[] = $parser->parse(
//            Forms::button()->label('ssssssssss')->add(
//                Forms::button()->subtype('default')->label('AAA'),
//                Forms::button()->subtype('default')->label('BBB')->style('default'),
//                Forms::button()->subtype('default')->label('CCC')->style('danger'),
//                )
//        );

//        $inputs[] = $parser->parse(
//            Forms::button()->btnBeforeIcon('fa fa-users')->btnAfterIcon(
//                Forms::button()->subtype('default')->label('sss')
////                Forms::button()->add(
////                    Forms::button()->subtype('default')->label('sss')
////                )
//            )->subtype('input')
//        );
//        print_r(Forms::checkbox('asdfsdfa','bb','ssss')->choices([['text'=>'aaa','value'=>'1'],['text'=>'bbb','value'=>'1']]));
//        exit;
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

//        $inputs[] = $parser->parse(
//            Forms::select('asdfsdfa','bbx','2','select')->choices([['text'=>'aaa','value'=>'1'],['text'=>'bbb','value'=>'2']])->isTags(true)//->multiple(true)
//            ->labelWidth(3)->rows(1)
//        );

//        $inputs[] = $parser->parse(
//            Forms::form($this->url('update'))->add(
//                Forms::input('aaa','用户')->subtype('color'),
//                Forms::input('bbb','秘密')->subtype('password'),
//                Forms::file('xxxx')->accept('image/*')->placeHolder('用户头像'),//->setCorpSize(200,400),//->label('用户头像')->labelWidth(4)->description('头像必须300x234'),
//                Forms::file('yyyy[]')->accept('image/*')->subtype('multiple')->setCorpSize(200,200),//->label('用户头像')->labelWidth(4)->description('头像必须300x234'),
//                Forms::button('重置')->action('reset'),
//                Forms::button('提交')->action('submit'),
//            )
//        );

        $inputs[] = $parser->parse(
//            Forms::form($this->url('update'))->add(
                Components::table('浏览器分布情况')->query(
                    [
                        'filters'=>[
                            'op'=>'AND',
                            'sub'=>[
                                ['key'=>'e','op'=>'>=','val'=>'11'],
                                ['key'=>'e','op'=>'<','val'=>'1001'],
                                ['key'=>'d','op'=>'>=','val'=>'11'],
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
                        ['name'=>'a', 'text'=>'字段A','tooltip'=>'这个是字段A','sort'=>1, 'filter'=>1, 'edit'=>1, 'delete'=>'canDelete', 'width'=>200, 'show'=>1, 'type'=>'text','params'=>[], 'icon'=>'fa fa-users', 'class'=>''],
                        ['name'=>'b', 'text'=>'字段B','tooltip'=>'这个是字段B','sort'=>1, 'filter'=>1, 'edit'=>1, 'delete'=>'canDelete', 'width'=>200, 'show'=>1, 'type'=>'text','params'=>[], 'icon'=>'fa fa-users', 'class'=>''],
                        ['name'=>'c', 'text'=>'字段C','tooltip'=>'这个是字段C','sort'=>1, 'filter'=>1, 'edit'=>1, 'delete'=>'canDelete', 'width'=>200, 'show'=>1, 'type'=>'text','params'=>[], 'icon'=>'fa fa-users', 'class'=>''],
                        ['name'=>'d', 'text'=>'字段D','tooltip'=>'这个是字段D','sort'=>1, 'filter'=>1, 'edit'=>1, 'delete'=>'canDelete', 'width'=>200, 'show'=>1, 'type'=>'text','params'=>[], 'icon'=>'fa fa-users', 'class'=>''],
                        ['name'=>'e', 'text'=>'字段E','tooltip'=>'这个是字段E','sort'=>1, 'filter'=>1, 'edit'=>1, 'delete'=>'canDelete', 'width'=>200, 'show'=>1, 'type'=>'text','params'=>[], 'icon'=>'fa fa-users', 'class'=>''],
                    ]
                )->primary('c')->deleteApi(
                    $this->url('delete',['item_id'=>'{id}'])
                )->description('测试表格组件'),
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
