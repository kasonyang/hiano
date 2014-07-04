<?php

/**
 * 
 * @author kasonyang <i@kasonyang.com>
 */

namespace Hiano\View;

interface ViewDriverInterface{
    /**
     * @param array $dirs 目录数组
     */
    function setTemplateDirs($dirs);
    /**
     * 渲染模块
     * @param string $template_file 模板文件
     * @param array $vars 变量关联数组
     */
    function render($template_file,$vars);
}

class View {

    private $vars, $tpl_dirs, $tpl,$version,$auto_display;
    
    /**
     *
     * @var ViewDriverInterface
     */
    private $driver = NULL;
            
    function setDriver($driver){
        $this->driver = $driver;
    }
    
    /**
     * 
     * @return ViewDriverInterface
     */
    function getDriver(){
        return $this->driver;
    }
    
    /**
     * 设置自动渲染模板
     * @param boolean $enable 是否自动渲染模板
     */
    function setAutoDisplay($enable){
        $this->auto_display = $enable;
    }
    
    /**
     * 返回当前自动渲染模板设置
     * @return boolean
     */
    function getAutoDisplay(){
        return $this->auto_display;
    }


    /**
    * 设置视图版本
    * 
    * 通过此函数，可以实现对多版本视图的支持，例如手机版、英文版等，此函数必须配合模板
    * 使用，模板命名规则：{action}.{version}.tpl
    * 
    * @param string $version 版本名
    */
   function setVersion($version){
       $this->version = $version;
   }

   /**
    * 获取当前版本
    * 
    * @return string
    */
   function getVersion(){
       return $this->version;
   }
   
   /**
    * 设置模板目录，用于改变模板目录（如果没有调用此函数，则使用默认目录）
    * 
    * @param array $tpl_dirs 目录
    */
    function setTemplateDir($tpl_dirs) {
        $this->tpl_dirs =  $tpl_dirs;
    }

    function addTemplateDir($dir){
        $this->tpl_dirs[] = $dir;
    }
    /**
     * 设置模板文件名（相对路径，不带.tpl后缀，如果没有调用此函数，则使用默认文件名）
     * @param string $tpl 模板文件
     */
    function setTemplate($tpl) {
        $this->tpl = $tpl;
    }

    /**
     * 读取模板文件名
     * @return string
     */
    function getTemplate() {
        return $this->tpl;
    }

    /**
     * 手动输出视图，一般情况你的代码里不需要调用此函数，系统会自动调用
     * @param string $tpl 模板文件名
     */
    function display($tpl = null) {
        $tpl_dirs = $this->tpl_dirs;
        if (!$tpl) {
            $version = $this->getVersion();
            $suffix = ($version ? '.' . $version : '' ) . '.tpl';
            $tpl = $this->tpl . $suffix;
        }
        if(!$this->driver){
            throw new Exception('View Driver Undefined');
        }
        $this->driver->setTemplateDirs($tpl_dirs);
        echo $this->driver->render($tpl, $this->vars);
    }

    /**
     * 赋值模板变量
     * @param string $names 变量名
     * @param string $value 变量值
     */
    function set($names,$value=NULL){
        if(!is_array($names)){
            $data = array($names => $value);
        }else{
            $data = $names;
        }
        foreach ($data as $key => $value) {
            $this->vars[$key] = $value;
        }
    }
    
    /**
     * 读取模板变量
     * @param string $name 变量名
     * @return mixed
     */
    function get($name){
        return $this->vars[$name];
    }
}
