<?php
namespace Power\Controllers;
use Phalcon\Mvc\Controller;
use Power\Models\UserConfigs;
use Power\Models\Configs;
use Power\Models\Users;
use Utils;
use PA;

class AuthorizationController extends Controller{
    function loginPageAction(){
        $config = Configs::getConfigsByUser();
        $this->view->site = $config['attribute'][0];
        $this->view->setViewsDir(POWER_VIEW_DIR);
        $this->view->pick('index/login');
    }
    
    function logoutAction(){
        setcookie(PA::$config['cookie_name'], "", time() - 3600,'/');
        return $this->response->redirect(PA_URL_PATH.'login', true);
    }
    
    function loginAction(){
        $this->view->disable();
        $login_error_times = Utils::cache('login:error') ?: 0;
        if($login_error_times>=3) return $this->response->redirect(PA_URL_PATH.'login?lock=1&error=错误登录次数太多，请在30分钟后再试！', true);
        
        $user_info = Users::findFirst(['name=?0 and is_enabled=1','bind'=>[$_REQUEST['user']]]);
        
        if(empty($user_info) || !password_verify($_REQUEST['password'],$user_info->password)){
            Utils::cache('login:error', $login_error_times+1, 30 * 60); // 30分钟
            return $this->response->redirect(PA_URL_PATH.'login?error=用户或密码错误', true);
        }
        $user_info = $user_info->toArray();
        $user_info['user_id']    = (int)$user_info['user_id'];
        $user_info['login_ip']   = Utils::ip();
        $user_info['login_time'] = time();
        $token = PA::$config['cookie_maker']($user_info);
        # 设置有效期为2小时
        setcookie(PA::$config['cookie_name'], $token, time()+7200, '/', '', !empty($_SERVER['HTTPS']), true);
        return $this->response->redirect(PA_URL_PATH, true);
    }
}
