<?php
namespace plugins\Tables\Controllers;
use Power\Controllers\AdminBaseController;

class ManagerController extends AdminBaseController
{
    public function settingsAction(){
        $this->title = 'Tables插件设置';
        $this->render();
    }
}