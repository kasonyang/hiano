<?php

/**
 * 
 * @author Kason Yang <i@kasonyang.com>
 */

namespace AppDemo\Model;

class DemoWidget extends \Hiano\Widget\Widget{
    function __construct() {
        parent::__construct();
        $this->assign('content','Hello,Hiano!');
    }
}