<?php

/**
 * 
 * @author kasonyang <i@kasonyang.com>
 */

namespace Hiano\Widget;

class Widget{
    
    private $vars;
    
    function __construct() {
        $this->assign('this', $this);
    }

    /**
     * 模板赋值
     * @param string|array $name
     * @param mix $value
     */
    function assign($name,$value=null){
        if(is_array($name)){
            foreach($name as $vk => $v){
                $this->vars[$vk] = $v;
            }
        }else{
            $this->vars[$name] = $value;
        }   
    }
    /**
     * 渲染视图
     * @param string $version 模板版本
     * @return string
     * @throws Exception
     */
    function render($version = NULL) {
        $view = \Hiano\App\App::newView();
        $name = get_class($this);
        if(substr($name, -6) !== 'Widget'){
            throw new \Exception('错误的Widget类名:' . $name );
        }
        $short_name = substr($name, 0,-6);
        $suffix = '.tpl';
        if($version){
            $suffix = '.' . $version . $suffix;
        }
        $tpl_file = HIANO_APP_PATH . '/Widget/' . $short_name . $suffix;
        return $view->render($tpl_file, $this->vars);
    }
    function __toString() {
        return $this->render();
    }
}

