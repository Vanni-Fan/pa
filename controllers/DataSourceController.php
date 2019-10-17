<?php
namespace Power\Controllers;

use HtmlBuilder\Parser\AdminLte\Parser;

class DataSourceController extends AdminBaseController{
    function indexAction(){
        $hb_parser = new Parser();
        $contents  = $hb_parser->parse(
            new Tab
        );
        $this->render();
    }
}
