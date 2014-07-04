<?php
namespace AppDemo\Controller\Home;
use AppDemo\Model;
class IndexController extends \Hiano\Controller\Controller{
    function indexAction(){
        $user = new Model\User(['id' => 1]);
    }
}