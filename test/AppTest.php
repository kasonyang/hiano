<?php

/**
 * 
 * @author Kason Yang <i@kasonyang.com>
 */

include_once __DIR__ . '/../libs/App.php';

class AppTest extends PHPUnit_Framework_TestCase{
    function testApp(){
        $this->assertEquals('', Hiano\App\App::getBaseUrl());
    }
}