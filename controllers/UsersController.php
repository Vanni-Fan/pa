<?php
namespace Power\Controllers;
use Power\Models\Users;
use Power\Models\Roles;

class UsersController extends AdminBaseController {
    protected $title = '用户管理';
    public function indexAction(){
        $this->current_page = (int)$this->getParam('page') ?: 1;
        $this->view->users  = Users::find(['offset'=>($this->current_page-1) * $this->page_size,'limit' => $this->page_size]);
        $this->view->page   = $this->getPaginatorString(Users::count());
        $this->view->enable_url = $this->url('display');
        $this->render('', $this->item_id ? true : false);
    }
    
    public static function getItemOwner($item_id = null): int
    {
        return $item_id;
    }
    
    public function newAction(){
        $this->subtitle = '添加新用户';
        $this->view->new = true;
        $this->view->roles = Roles::find();
        $this->view->item_id = $this->item_id;
        $this->render('users/edit');
    }
    
    public function displayAction(){
        $this->subtitle = '修改用户信息';
        if($this->getParam('type')=='enable'){
            $user = Users::findFirst($this->item_id);
            $user->enabled = $this->getParam('enabled');
            $user->save();
            header('content-type:application/json;charset=utf8');
            return json_encode(['ok'=>1]);
        }else{
            $this->view->new = false;
            $this->view->roles = Roles::find();
            $this->view->item_id = $this->item_id;
            $this->view->user = Users::findFirst($this->item_id);
//            exit;
            $this->render('users/edit');
        }
    }
    
    public function updateAction(){
        $data = $_POST;
        $data['enabled'] = 1;
        if($this->item_id){
            $user = Users::findFirst($this->item_id);
            if(empty($data['password'])){
                unset($data['password']);
            }else{
                $data['password'] = password_hash($data['password'],PASSWORD_DEFAULT);
            }
            $user->save($data);
        }else{
            $user = new Users();
            $data['password'] = password_hash($data['password'],PASSWORD_DEFAULT);
            $user->save($data);
        }
//        exit($this->url('index'));
        $this->response->redirect($this->url('index'),true);
    }
    
    public function deleteAction(){
        $user = Users::findFirst($this->item_id);
        $user->delete();
        $this->jsonOut(['code'=>'ok']);
//        $this->response->redirect($this->url(),true);
//        $this->indexAction();
    }

}
