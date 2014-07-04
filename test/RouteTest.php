<?php

/**
 * 
 * @author kasonyang <i@kasonyang.com>
 */

include __DIR__ . '/../libs/Route.php';
use \Hiano\Route;

class RouteTest extends PHPUnit_Framework_TestCase{
    
    function testRoute(){
        $route = new Hiano\Route\Route(':mod/:ctrl/:act');
        $router = new Hiano\Route\Router();
        $router->addRoute('test', $route);
        
        $ps = $router->parse('m/c/a');
        $this->assertEquals(['mod'=>'m','ctrl'=>'c','act'=>'a'],$ps);
        
        $url = $router->format(['mod'=>'m','ctrl'=>'c','act'=>'a']);
        $this->assertEquals('m/c/a', $url);
    }
    
    function testRouteStatic(){
        $params = ['m' => 'mm','c' => 'cc'];
        $router = new Route\Router();
        $route = new Route\Route('',$params);
        $router->addRoute('test', $route);
        $this->assertEquals($params, $router->parse(''));
    }
    
}

class UrlTest extends PHPUnit_Framework_TestCase{
    
    function testStandardUrl(){
        $params  = ['a' => 'aa','b' => 'bb'];
        
        $s_url = new Hiano\URL\StandardUrl('f?a=aa&b=bb');
        $this->assertEquals($params, $s_url->getQuery());
        
        $s_url->setQuery('c', 'cc');
        $this->assertEquals('f?a=aa&b=bb&c=cc', $s_url->build());
        
        $this->assertEquals($params,  URL\StandardUrl::query2array('a=aa&b=bb'));
        $this->assertEquals('a=aa&b=bb', URL\StandardUrl::array2query($params));
        
    }
    
    function testInnerUrl(){
        $i_url = new URL\InnerUrl('m', 'c', 'a');
        
        $this->assertEquals(['module' => 'mm','controller' => 'cc','action'=> 'aa'],$i_url->parse('mm/cc/aa'));
        $this->assertEquals(['module' => 'm','controller' => 'cc','action'=> 'aa'],$i_url->parse('cc/aa')); 
        $this->assertEquals(['module' => 'm','controller' => 'c','action'=> 'aa'], $i_url->parse('aa'));
        
    }
    
    function testUrlFactory(){
        $router = new Route\Router();
        $route = new Route\Route(':m/:c/:a',NULL,NULL, Route\Route::TYPE_DYMATIC);
        $router->addRoute('route1', $route);
        $uf = new URL\UrlFactory($router);
        
        $arr = ['m' => 'mm','c' => 'cc','a' => 'aa'];
        $url = 'mm/cc/aa';
        
        $this->assertEquals($arr, $uf->parse('mm/cc/aa'));
        $this->assertEquals($url, $uf->format($arr));
    }
}