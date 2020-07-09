<?php
namespace Power\Controllers;
use HtmlBuilder\Components;
use HtmlBuilder\Element;
use HtmlBuilder\Forms;
use HtmlBuilder\Forms\Form;
use HtmlBuilder\Layouts;
use HtmlBuilder\Parser\AdminLte\Parser;
use HtmlBuilder\Validate;
use Power\Models\Users;
use Power\Models\Roles;

class UsersController extends AdminBaseController {
    protected $title = '用户管理';
    public function indexAction(){
        $parser = new Parser();
        $parser->style(/** @lang CSS */'
            .user_image{padding:0 !important;line-height: 35px !important;text-align:center;}
            .user_image img{width:35px;height:35px;}
        ');
        $this->view->contents = $parser->parse(
            Components::table('用户列表')->fields([
                ['name'=>'user_id', 'text'=>'用户ID','sort'=>1, 'filter'=>1,'class'=>'text-center'],
                ['name'=>'role_id', 'text'=>'角色ID','sort'=>1, 'show'=>0, 'filter'=>1],
                ['name'=>'role_name', 'text'=>'角色名','sort'=>1, 'filter'=>1,'class'=>'text-center'],
                ['name'=>'name', 'text'=>'登录名','sort'=>1, 'filter'=>1,'class'=>'text-center'],
                ['name'=>'nickname', 'text'=>'显示名','sort'=>1, 'filter'=>1,'class'=>'text-center'],
                ['name'=>'password', 'text'=>'密码','class'=>'text-center'],
                ['name'=>'image', 'text'=>'头像','render'=>'i=>i?("<img src=\'" + i + "\'>"):""', 'class'=>'user_image'],
                ['name'=>'mobile', 'text'=>'手机号','sort'=>1, 'filter'=>1],
                ['name'=>'is_enabled', 'text'=>'是否可用','sort'=>1, 'filter'=>1,'render'=>'v=>v==1?"已启用":"已禁用"','class'=>'text-center'],
                ['name'=>'remark', 'text'=>'备注','sort'=>1, 'show'=>0, 'filter'=>1],
                ['name'=>'created_time', 'text'=>'创建时间','sort'=>1, 'show'=>0, 'filter'=>1, 'render'=>'i=>i?new Date(i*1000).toLocaleString():""'],
                ['name'=>'updated_time', 'text'=>'更新时间','sort'=>1, 'show'=>0, 'filter'=>1, 'render'=>'i=>i?new Date(i*1000).toLocaleString():""'],
                ['name'=>'created_user', 'text'=>'创建者ID','sort'=>1, 'show'=>0, 'filter'=>1],
                ['name'=>'updated_user', 'text'=>'更新者ID','sort'=>1, 'show'=>0, 'filter'=>1],
                ['name'=>'created_name', 'text'=>'创建者','show'=>0, 'filter'=>0],
                ['name'=>'updated_name', 'text'=>'更新者','show'=>0, 'filter'=>0],
            ])->canEdit(true)->primary('user_id')->canMin(false)
                ->queryApi($this->url('list'))
                ->createApi($this->url('append'))
                ->deleteApi($this->url('delete',['item_id'=>'_ID_']))
                ->updateApi($this->url('update',['item_id'=>'_ID_']))
                ->description(
                    ['点击[字段]可以展示或隐藏更多的列','[筛选]功能可以帮您精确查找出内容'][random_int(0,1)]
                )
        );
        $parser->setResources($this);
        return $this->render(null);
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
        if(isset($_POST['filters'])) Users::parseWhere(['where'=>$_POST['filters']],$where);

        $data = [];
        foreach(Users::find($where) as $row){
            $d = $row->toArray();
//            var_dump($row->Menu->toArray());
            $d['password']     = '******';
            $d['role_name']    = $row->Role ? $row->Role->name : '';
            $d['created_name'] = $row->CreatedUser ? $row->CreatedUser->nickname : '';
            $d['updated_name'] = $row->UpdatedUser ? $row->UpdatedUser->nickname : '';
            $data[] = $d;
        }

        $this->jsonOut(
            [
                'list'=>$data,
                'total'=>Users::count(['conditions'=>$where['conditions'],'bind'=>$where['bind']]),
//                'total'=>500,
                'page'=>(int)$page,
                'size'=>(int)$size,
                'query'=>[
                    'filter'=>$_POST['filters']??[],
                    'limit'=>$_POST['limit']??['page'=>$page, 'size'=>$size]
                ]
            ]
        );
    }

    public function newAction(){ return $this->displayAction(); }
    public function displayAction(){
        $parser = new Parser();
        $all_roles = [];
        foreach(Roles::find(['is_enabled=1']) as $row){
            $all_roles[] = ['text'=>$row->name, 'value'=>$row->role_id];
        };
        $default = [];
        if($this->item_id){
            $this->title = '修改用户信息';
            $default = Users::findFirst($this->item_id)->toArray();
        }else{
            $this->title = '添加用户';
        }
        $dom = Forms::form($this->url('update'),'POST')->add(
            Layouts::columns()->column(
                Element::create('div')->style('display:block')->add(
                    Forms::input('name','登录名',$default['name']??'')->required(true)->labelWidth(3)->id('user_name')
                        ->validate(
                            Validate::callback('用户名已存在','userExists')
                        ),
                    Forms::input('password','密码','','password')->labelWidth(3)->id('pass_1')
//                        ->validate(Validate::expression('两次密码不匹配','$("#pass_1-input").val()==$("#pass_2-input").val()')),
                        ->validate(Validate::callback('两次密码不匹配','checkPass')),
                    Forms::select('role_id','选择角色',$default['role_id']??1)->required(true)->labelWidth(3)->choices($all_roles),
                    Forms::input('mobile','手机号',$default['mobile']??'','mobile')->labelWidth(3),
                    Forms::button('','返回')->action('button')->style('default')->on('click','history.back()')
                )
            ,6)->column(
                Element::create('div')->style('display:block')->add(
                    Forms::input('nickname','显示名',$default['nickname']??'')->required(true)->labelWidth(3),
                    Forms::input('password_2','密码确认','','password')->labelWidth(3)->id('pass_2')
//                        ->validate(Validate::expression('两次密码不匹配','$("#pass_1-input").val()==$("#pass_2-input").val()')),
                        ->validate(Validate::callback('两次密码不匹配','checkPass')),
                    Forms::radio('is_enabled','是否可用',$default['is_enabled']??1)->required(true)->labelWidth(3)->choices([['value'=>1,'text'=>'启用'],['value'=>0,'text'=>'禁用']]),
                    Forms::file('image','头像',$default['image']??'')->labelWidth(3)->accept('image/*')->corpWidth(100)->returnUrl($this->url('index')),
                    Forms::button('','提交')->class('pull-right')->action('submit'),
                )
            ,6),
        )->on('submit','checkForm');
        $this->view->contents = $parser->parse($dom);

        $this->addScript("
            function checkForm(){
                let p1 = $('#pass_1-input').val();
                let p2 = $('#pass_2-input').val();
                if(!p1 && !p2 && {$this->item_id}){
                    return true;
                }
                if(!p1){ HB_input_error('pass_1','请输入密码'); return false; }
                if(!p2){ HB_input_error('pass_2','请确认密码'); return false; }
                return true;
            }
            function checkPass(){
                let p1 = $('#pass_1-input').val();
                let p2 = $('#pass_2-input').val();
                if(!p1 || !p2) return true;
                if(p1 == p2) {
                    HB_input_ok('pass_1') && HB_input_ok('pass_2');
                    return true;
                }else{
                    return false;
                } 
            }
            function userExists(){
                let status = false;
                let msg = '';
                $.ajax({
                    url:'" . $this->url('update',['item_id'=>$this->item_id,'action'=>'exists']) . "?name='+$('#user_name-input').val(),
                    async:false,
                    success:d=>{
                        status = d.ok;
                        msg = d.msg;
                    }
                });
                if(!status){
                    HB_input_error('user_name',msg);
                }else{
                    HB_input_ok('user_name');
                }
                return status;
            }
        ");
        $parser->setResources($this);
        return $this->render(null);
    }
    
    public function updateAction(){
        $data = $_POST;
//        print_r($data);
//        print_r($_FILES);
//        print_r($_SERVER);

        if(!empty($_FILES['image'])){
            $file = $_FILES['image'];
            $file_suufix = '/uploads/users/'.uniqid().'.png';
            $target = $_SERVER['DOCUMENT_ROOT'].$file_suufix;
            if(!is_dir(dirname($target))) mkdir(dirname($target),0700,true);
            if(strpos($file['type'],'image/')!==false){
                move_uploaded_file($file['tmp_name'],$target);
                $data['image'] = $file_suufix;
            }
        }
        $data['mobile'] = str_replace(' ','',$data['mobile']);

        if($this->item_id){
            $user = Users::findFirst($this->item_id);
            if(empty($data['password'])){
                unset($data['password']);
            }else{
                $data['password'] = password_hash($data['password'],PASSWORD_DEFAULT);
            }
            if(isset($data['image'])){
                if($user->image && file_exists($_SERVER['DOCUMENT_ROOT'].$user->image)){
                    unlink($_SERVER['DOCUMENT_ROOT'].$user->image);
                }
            }
            $user->assign($data)->save();
        }else{
            $user = new Users();
            $data['password'] = password_hash($data['password'],PASSWORD_DEFAULT);
            $user->assign($data)->save();
        }
        $this->response->redirect($this->url('index'),true);
    }
    
    public function deleteAction(){
        if(is_array($this->item_id)){
            $user = Users::find([
                'user_id IN ({ids:array})',
                'bind'=>[
                    'ids'=>$this->item_id
                ]
            ]);
            foreach($user as $u){
                if($u->image && file_exists($_SERVER['DOCUMENT_ROOT'].$u->image)){
                    unlink($_SERVER['DOCUMENT_ROOT'].$u->image);
                }
            }
        }else{
            $user = Users::findFirst($this->item_id);
            if($user->image && file_exists($_SERVER['DOCUMENT_ROOT'].$user->image)){
                unlink($_SERVER['DOCUMENT_ROOT'].$user->image);
            }
        }
        $user->delete();
        return $this->listAction();
    }

    public function existsAction(){
        $name = $this->request->getQuery('name');
        $len  = strlen($name);
        if($len>10 || $len<3) return $this->jsonOut(['msg'=>'用户名的长度在3到10之间','ok'=>false]);

        if($this->item_id){
            $where = ['name = ?0 and user_id != ?1','bind'=>[$name, $this->item_id]];
        }else{
            $where = ['name = ?0','bind'=>[$name]];
        }

        if(Users::findFirst($where)){
            $this->jsonOut(['msg'=>'用户名已存在','ok'=>false]);
        }else{
            $this->jsonOut(['msg'=>'','ok'=>true]);
        }
    }
}
