<?php
namespace AppDemo\Controller\Home;
use AppDemo\Model;
class IndexController extends \Hiano\Controller\Controller{
    function indexAction(){
        $widget = new Model\DemoWidget();
        $this->view->set('demowidget', $widget);
    }
}