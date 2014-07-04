<?php

/**
 * 
 * @author kasonyang <i@kasonyang.com>
 */

include_once __DIR__ . '/../libs/Filter.php';


class Vars{
    static $f1_executed;
    static $f2_executed;
    static $callback_executed;
}

class Filter1 extends Hiano\Filter\Filter{
    
    public function execute($filter_chain) {
        parent::execute($filter_chain);
        Vars::$f1_executed = TRUE;
        $filter_chain->execute();
    }
}

class Filter2 extends Hiano\Filter\Filter{
    public function execute($filter_chain) {
        parent::execute($filter_chain);
        Vars::$f2_executed = TRUE;
        $filter_chain->execute();
    }
}

class FilterTest extends PHPUnit_Framework_TestCase{
    function testExecute(){
        $f1 = new Filter1();
        $f2 = new Filter2();
        $fc = new \Hiano\Filter\FilterChain(function(){
            Vars::$callback_executed = TRUE;
        });
        $fc->addFilter($f1);
        $fc->addFilter($f2);
        $fc->execute();
        
        $this->assertEquals(TRUE, Vars::$f1_executed);
        $this->assertEquals(TRUE, Vars::$f2_executed);
        $this->assertEquals(TRUE, Vars::$callback_executed);
    }
}