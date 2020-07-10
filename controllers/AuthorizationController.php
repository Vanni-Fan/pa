<?php
namespace Power\Controllers;
use Phalcon\Mvc\Controller;
use Power\Models\UserConfigs;
use Power\Models\Configs;
use Power\Models\Users;
use Utils;
use PA;
use Phalcon\Text;
use Phalcon\Chart\Captcha;
use Phalcon\Image\Adapter\Gd;

class AuthorizationController extends Controller{
    function loginPageAction(){
        $captchaUrl = '/' . PA::$config['pa_url_path'] . '/captcha';
        $config = Configs::getConfigsByUser();
        $this->view->captchaUrl = $captchaUrl;
        $this->view->site = $config['attribute'][0];
        $this->view->setViewsDir(POWER_VIEW_DIR);
        $this->view->pick('index/login');
    }

    public function initialize() {
        session_start();
    }
    
    function logoutAction(){
        setcookie(PA::$config['cookie_name'], "", time() - 3600,'/');
        return $this->response->redirect(PA_URL_PATH.'login', true);
    }
    
    function loginAction(){
        $from = rawurlencode(isset($_GET['from']) ? $_GET['from'] : '');
        
        $this->view->disable();
        $login_error_times = Utils::cache('login:error') ?: 0;

        $code = isset($_REQUEST['code']) ? $_REQUEST['code'] : '';
        if (!$this->verifyCaptcha($code)) {
            return $this->response->redirect(PA_URL_PATH.'login?error=验证码错误'.($from ? "&from=$from" : ''), true);
        }

        if($login_error_times>=3) return $this->response->redirect(PA_URL_PATH.'login?lock=1&error=错误登录次数太多，请在30分钟后再试！'.($from ? "&from=$from" : ''), true);
        
        $user_info = Users::findFirst(['name=?0 and is_enabled=1','bind'=>[$_REQUEST['user']]]);
        
        if(empty($user_info) || !password_verify($_REQUEST['password'],$user_info->password)){
            Utils::cache('login:error', $login_error_times+1, 30 * 60); // 30分钟
            return $this->response->redirect(PA_URL_PATH.'login?error=用户或密码错误'.($from ? "&from=$from" : ''), true);
        }
        $user_info = $user_info->toArray();
        $user_info['user_id']    = (int)$user_info['user_id'];
        $user_info['login_ip']   = Utils::ip();
        $user_info['login_time'] = time();
        $token = PA::$config['cookie_maker']($user_info);
        # 设置有效期为2小时
        setcookie(PA::$config['cookie_name'], $token, time()+7200, '/', '', !empty($_SERVER['HTTPS']), true);

        if ($from) {
            $url = rawurldecode($from);
            return $this->response->redirect($url, true);
        }

        $url = $this->getHomeUrl();
        $routeUrl = PA_URL_PATH;
        if ($url) {
            $routeUrl = $url;
        }
        return $this->response->redirect($routeUrl, true);
    }

    protected function getHomeUrl() {
        $configs = Configs::getConfigs('attribute');
        $url = '';
        foreach ($configs as $cfg) {
            if ($cfg['var_name'] == 'home_url') {
                $url = $cfg['var_default'];
                break;
            }
        }

        return $url;
    }

    function captchaAction() {
        $code = Text::random(Text::RANDOM_ALNUM, 4);
        $_SESSION['captcha'] = $code;

        $image = imagecreatetruecolor(100, 30);    
        $bgcolor = imagecolorallocate($image,255,255,255); //#ffffff
        imagefill($image, 0, 0, $bgcolor);

        //设置坐标
        for ($i=0; $i<strlen($code); $i++) {
            $fontcolor = imagecolorallocate($image, rand(0,120),rand(0,120), rand(0,120));
            $x = ($i*100/4)+rand(5,10);
            $y = rand(5,10);
            $char = $code[$i];
            imagestring($image,6,$x,$y,$char,$fontcolor);
        }

        $_SESSION['captcha'] = $code;
 
        header('Content-Type: image/png');
        imagepng($image);
        imagedestroy($image);
    }

    // 判断验证码是否正确
    protected function verifyCaptcha($code) {
        $captcha = $_SESSION['captcha'];
        if (strtolower($captcha) != strtolower($code)) {
            return false;
        }

        return true;
    }
}
