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
        $view_driver = \Hiano\App\App::newViewDriver();
        $name = get_class($this);
        if(substr($name, -6) !== 'Widget'){
            throw new \Exception('错误的Widget类名:' . $name );
        }
        $short_name = substr($name, 0,-6);
        $suffix = '.tpl';
        if($version){
            $suffix = '.' . $version . $suffix;
        }
        $file_name = str_replace('\\', '/', $short_name);
        $tpl_file = HIANO_APP_PATH . '/Widget/' . $file_name . $suffix;
        $tpl_dir = dirname($tpl_file);
        $tpl_basename = basename($tpl_file);
        $view_driver->setTemplateDirs((array)$tpl_dir);
        return $view_driver->render($tpl_basename, $this->vars);
    }
    function __toString() {
        return $this->render();
    }
}

