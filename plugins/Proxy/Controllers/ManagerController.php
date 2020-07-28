<?php
namespace plugins\Proxy\Controllers;

use HtmlBuilder\Components;
use HtmlBuilder\Forms;
use HtmlBuilder\Parser\AdminLte\Parser;
use plugins\Proxy\Models\ProxyConfigs;
use plugins\Proxy\Models\ProxyMenus;
use Power\Controllers\AdminBaseController;


class ManagerController extends AdminBaseController
{
    private $params = [];
    public function initialize()
    {
        $this->params = $this->getParam();
        unset($this->params['Rule']);
        $this->template_path = POWER_BASE_DIR . 'plugins/Proxy/views/templates/';
        return parent::initialize();
    }

    /**
     * 获得子事件的URL
     * @param array $param
     * @param string $method
     * @return string
     * @throws \Exception
     */
    private function getUrl(array $param, $method='GET'){
        return $this->url($method=='GET'?'display':'update', array_merge($this->params, $param));
    }

    /**
     * 设置主页面
     * @return mixed
     * @throws \Exception
     */
    public function settingsAction(){
        # 根据不同的子命令来跳转不同的方法
        if($this->getParam('command')) return $this->{$this->getParam('command')}();

        $this->title = 'Proxy插件设置';
        $this->subtitle = '管理代理菜单已经管理用户的流量配置';

        $table = new Parser();
        $this->view->proxy_table = $table->parse(
            Components::table('代理菜单列表')->fields([
                ['name'=>'proxy_id','text'=>'#ID'],
                ['name'=>'menu_name','text'=>'菜单名称'],
                ['name'=>'allow_urls','text'=>'允许的URLS'],
                ['name'=>'login_type','text'=>'登录类型'],
                ['name'=>'store_type','text'=>'存储类型']
            ])
                ->queryApi($this->getUrl(['command'=>'proxy_list']))
                ->createApi('javascript:alert("添加代理菜单")')
                ->deleteApi(['command'=>'delete_proxy'])
                ->canEdit(true)
        );
        $this->view->user_table  = $table->parse(
            Components::table('用户流量限制')->fields([
                ['name'=>'user_id','text'=>'#ID'],
                ['name'=>'user_name','text'=>'用户名'],
                ['name'=>'upload_banwidth','text'=>'上行带宽'],
                ['name'=>'download_banwidth','text'=>'下行带宽'],
                ['name'=>'clearing_type','text'=>'结算方式'],
                ['name'=>'enabled','text'=>'状态'],
                ['name'=>'expire','text'=>'到期时间'],
                ['name'=>'current_banwidth','text'=>'当前流量'],
            ])
                ->queryApi($this->getUrl(['command'=>'user_list']))
                ->deleteApi(['command'=>'delete_user'])
                ->createApi('javascript:alert("添加用户")')
                ->canEdit(true)
        );
        $this->view->back = $table->parse(Forms::button('back','返回')->on('click','history.back()'));
        $table->setResources($this);

        $this->addCss('https://cdn.jsdelivr.net/npm/@mdi/font@5.x/css/materialdesignicons.min.css');
        $this->addCss('https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css');
        $this->addJs('https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js');
        $this->render('manager-list');
    }

    function proxy_list(){
        [$data, $_menus] = [[], ProxyMenus::find()];
        iterator_apply($_menus, function (&$data, $interator){
            $item            = $interator->current()->toArray();
            $data[]          = $item;
            return true;
        },[&$data, $_menus]);

        $this->jsonOut([
            'list'=>$data,
            'total'=>1000,
            'page'=>5,
            'size'=>50,
        ]);
    }
    function user_list(){
        [$data, $_menus] = [[], ProxyConfigs::find()];
        iterator_apply($_menus, function (&$data, $interator){
            $item            = $interator->current()->toArray();
            $data[]          = $item;
            return true;
        },[&$data, $_menus]);

        $this->jsonOut([
            'list'=>$data,
            'total'=>1000,
            'page'=>5,
            'size'=>50,
        ]);
    }
}