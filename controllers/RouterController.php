<?php
namespace Power\Controllers;
use Power\Models\menus;
use Phalcon\Mvc\Controller;
use PA, Utils, Exception;

class RouterController extends Controller {
    /**
     * @param $action
     * @throws \Exception
     */
    private function forward($action){
        # 获得参数
        $param = Utils::getDispatchParamsByKey($this->dispatcher);
        # 获得Rule ID以及表中定义的参数
        $rule  = Menus::findFirstByMenuId($param['menu_id']);
        if(!$rule)throw new Exception('Permission Denied.');
        $rule  = Menus::getRuleExtend($rule->toArray());
        $forward = $rule['router'];
        $forward['params'] = array_merge($rule['params'], $param);
        $forward['params']['Rule'] = $rule;
        if(isset($forward['params']['action'])){        // 扩展属性
            $forward['action'] = $forward['params']['action'];
        }elseif(isset($param['setting'])){  // 页面设置
            $forward['action'] = 'setting';
        }elseif(empty($forward['action'])){
            $forward['action'] = $action;
        }
        if(empty($forward['controller'])) $forward['controller'] = 'index';
        if(isset($forward['module'])){
            PA::$app->getModule($forward['module'])(); // 加载模块
            $this->dispatcher->setModuleName($forward['module']);
        }
        $this->dispatcher->forward($forward);
    }
    
    public function __call($name, $param){
        if(preg_match('/action$/i',$name)) $this->forward(substr($name,0,-6));
        else throw new Exception('Method No Not Exists!');
    }
}
