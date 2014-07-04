<?php

/**
 * 
 * @author kasonyang <i@kasonyang.com>
 */

namespace Hiano\App;

use \Hiano\Request;
use \Hiano\Controller;
use \Hiano\Filter;


class RedirectException extends \Exception {
    
}

class StopException extends \Exception {
    
}

class App {
    
    private static $model_path;
    
    private static $import_path;
    
    private static $filter;

    /**
     *
     * @var \Hiano\Router\Router
     */
    private static $router;
    
    /**
     *
     * @var \Hiano\Request\Request
     */
    private static $request;

    private static function loadFilter($filter_name) {
        $class_name = '\\' . self::getName() . '\\Filter\\' . $filter_name;
        if (!class_exists($class_name)){
            throw new \Exception('Failed to load Filter:' . $filter_name);
        }
        return new $class_name();
    }
    
    static function getName(){
        $dir = HIANO_APP_PATH;
        if (in_array(substr($dir, -1), ['\\', '/'])) {
            $dir = substr($dir, 0, -1);
        }
        return basename($dir);
    }

        /**
     * 返回请求URL
     * @return string
     */
    static function getUrl() {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * 返回入口文件的目录
     * @return string
     */
    static function getBaseUrl() {
        return substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/') + 1);
    }
    
    static function getMainUrl(){
        $url = self::getUrl();
        return substr($url, strlen(self::getBaseUrl()));
    }
    
    /**
     * 
     * @return \Hiano\Request\Request
     */
    static function getRequest(){
        if(!self::$request){
            $router = self::getRouter();
            $parameter = $router->parse(self::getMainUrl());
            self::$request = new Request\Request($parameter , $_POST, $_FILES, $_SERVER);
        }
        return self::$request;
    }
    /**
     * 运行前段控制器
     * @param string $module_name 模块名
     * @param string $controller_name 控制器名
     * @param string $action 动作名
     */
    static function run($module_name = null, $controller_name = null, $action = null) {
        $request = self::getRequest();
        if(self::getConfig()->get('security.csrf_defender')){
            if($request->isPost()){
                $csrf_token  = $request->getPost('_csrftoken');
                if($csrf_token != $request->getCookie('_csrftoken')){
                    exit('CsrfToken Invalid!');
                }
            }else{
                if(!$request->hasCookie('_csrftoken')){
                    setcookie('_csrftoken', \Hiano\Token\Token::generate('_csrftoken'), 0, '/');
                }
            }
        }
        
        
        $module_name or $module_name = self::getModuleName();// $request->getParameter('module');
        $controller_name or $controller_name = self::getControllerName();// $request->getParameter('controller');
        $action or $action = self::getActionName();// $request->getParameter('action');
        
        $app_path = realpath(HIANO_APP_PATH);
        $controller_dir = $app_path . '/Controller/';
        $view_dir = $app_path . '/View/';
        $module_main_file = $controller_dir . $module_name . '/main.php';
        if (file_exists($module_main_file)) {
            include $module_main_file;
        }
        
        $view = self::newView();
        
        $tpl_dir = $view_dir . '/' . ucfirst($module_name) . '/' . ucfirst($controller_name);
        $view->addTemplateDir($view_dir);
        $view->addTemplateDir($tpl_dir);
        $view->setTemplate($action);
        
        try {
            $ctrl = self::getController($module_name , $controller_name , $request , $view);
            $filter_chain = new Filter\FilterChain(function($ctrl,$view){
                $action_ret = $ctrl->dispatch(self::getActionName());
                if(is_array($action_ret)){
                    echo json_encode($action_ret);
                }elseif(is_string($action_ret)){
                    echo $action_ret;
                }elseif($action_ret === TRUE or $action_ret===NULL){
                    $view->display();
                }elseif($action_ret === FALSE){
                    //do nothing
                }else{
                    throw new \Exception('错误的动作返回值！');
                }
            },[$ctrl,$view]);
            if ($filters = self::$filter) {
                $filter_count = count($filters);
                for ($i = 0; $i < $filter_count; $i++) {
                    $f = self::loadFilter($filters[$i]);
                    $filter_chain->addFilter($f);
                }
            }
            $filter_chain->execute();
        } catch (Controller\NoActionException $e) {
            self::forward404();
        } catch (Controller\NoControllerException $e) {
            self::forward404();
        } catch (Controller\NoModuleException $e) {
            self::forward404();
        } catch (RedirectException $e) {
            
        } catch (StopException $e) {
            
        }
    }
    
    /**
     * 停止动作的执行，方式：抛出spStopException异常，该异常自动被系统捕获处理
     * @throws spStopException
     */
    static function stop(){
        throw new StopException();
    }

    /**
     * 
     * @param string $url
     * @throws spRedirectException
     */
    static function redirectOut($url = null) {
        if (!isset($url)){
            $url = self::getBaseURL();
        }
        header("Location:$url");
        throw new RedirectException;
    }

    static function getModuleName(){
        return self::getRequest()->getParameter('module');
    }
    
    static function getControllerName(){
        return self::getRequest()->getParameter('controller');
    }
    
    static function getActionName(){
        return self::getRequest()->getParameter('action');
    }


    /**
     * 
     * @param string $inner_url
     * @param string $return
     */
    static function redirect($inner_url = null , $return = null){
        $url = null;
        if($inner_url){
            $inner_url_obj = new \Hiano\Route\InnerUrl(self::getModuleName(), self::getControllerName(), self::getActionName());
            $url = self::getRouter()->format($inner_url_obj->parse($inner_url));
            if($return !== null and $return !==false){
                $return_url = is_bool($return) ? self::getUrl() : $return;
                $u = new \Hiano\Route\StandardUrl($url);
                $u->setQuery('return', $return_url);
                $url = $u->build();
            }
        }
        self::redirectOut($url);
    }

    /**
     * 
     */
    static function redirectReferer() {
        self::redirectOut($_SERVER['HTTP_REFERER']);
    }

    /**
     * 跳转到目前URL指定的URL（由URL里的return参数指定）
     * 出于安全方面的考虑，指定的URL必须以‘/’开头
     * @param string $default_outerurl
     */
    static function redirectRequest($default_outerurl='/') {
        $return_url = Request::getParameter('return');
        if($return_url and substr($return_url, 0,1)=='/'){
            $url = $return_url;
        }else{
            $url = $default_outerurl;
        }
        self::redirectOut($url);
    }

    /**
     * 将POST参数转为GET参数，并跳转，后面的代码不再执行
     * @param string $inner_url
     */
    static function redirectPostAsParameter($inner_url = null){
        if($inner_url === NULL){
            $inner_url_arr = self::$request->getParameter();
        }else{
            $inner_url_obj = new \Hiano\Route\InnerUrl(self::getModuleName(), self::getControllerName(), self::getActionName());
            $inner_url_arr = $inner_url_obj->parse($inner_url);
        }
        $ps = self::$request->getPost();
        $arr = array_merge($ps, $inner_url_arr);
        $url = self::getRouter()->format($arr);
        self::redirectOut($url);
    }

    /**
     * 返回404状态码
     */
    static function forward404() {
        header('HTTP/1.0 404 Not Found');
        exit();
    }

    /**
     * 转到错误页面，后面的代码不再执行
     * @param string $err_msg 错误描述
     * @param string $error_tpl_id 错误页面使用的模板
     * @throws StopException
     */
    static function forwardError($err_msg = '错误的请求！', $error_tpl_id = null) {
        exit($err_msg);
    }

    /**
     * 向浏览器发送文件（弹出下载对话框）
     * @param string $file_path 要发送的文件
     * @param string $display_name 显示的文件名
     */
    static function sendFile($file_path,$display_name = null){
        if($display_name ===null){
            $display_name = basename($file_path);
        }
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename='.urlencode($display_name));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Content-Length: '.filesize($file_path));
        readfile($file_path);
    }
    
    /**
     * 以文件的形式向浏览器发送字符串（弹出下载对话框）
     * @param string $str
     * @param string $display_name
     */
    static function sendStringAsFile($str,$display_name){
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename='.urlencode($display_name));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Content-Length: '. strlen($str));
        echo $str;
    }


    /**
     * 返回控制器
     * @param string $controller_name 控制器名
     * @return \Hiano\Controller\Controller
     * @throws \Hiano\Controller\NoControllerException
     */
    static function getController($module_name,$controller_name,$request,$view) {
        static $ctrls = NULL;
        $module_name  = strtolower($module_name);
        $controller_name = strtolower($controller_name);
        if (!$ctrls[$module_name][$controller_name]) {
            $controller = ucfirst($controller_name) . 'Controller';
            $controller_class = '\\' . self::getName() . '\\Controller\\' . ucfirst($module_name) . '\\' . $controller;
            if (!class_exists($controller_class)) {
                throw new Controller\NoControllerException;
            }
            $c = new $controller_class($request,$view);
            $ctrls[$module_name][$controller_name] = $c;
        }
        return $ctrls[$module_name][$controller_name];
    }

    /**
     * 注册过滤器
     * @param string $filter_name 过滤器名，不带.php后缀，如：baseFilter
     */
    static function registerFilter($filter_name) {
        self::$filter[] = $filter_name;
    }

    /**
     * 注册模型搜索路径
     * @param string $path 路径
     */
    static function registerModelPath($path) {
        if (self::$model_path){
            self::$model_path .= ';';
        }
        self::$model_path .= $path;
    }
    
    /**
     * 
     * @return string
     */
    static function getModelPath(){
        return self::$model_path;
    }

    /**
     * 
     * @param string $path 要注册的路径
     */
    static function registerImportPath($path) {
        if (self::$import_path){
            self::$import_path .= ';';
        }
        self::$import_path .= $path;
    }
    
    static function getImportPath(){
        return self::$import_path;
    }

    /**
     * 
     * @staticvar \Hiano\Config\Config $config
     * @return \Hiano\Config\Config
     */
    static function getConfig(){
        static $config;
        if(!$config){
            $config = new \Hiano\Config\Config();
            $config->setConfigFilePath(HIANO_APP_PATH . '/Config');
        }
        return $config;
    }
    
    /**
     * @return \Hiano\Route\Router
     */
    static function getRouter(){
        if(!self::$router){
            self::$router = new \Hiano\Route\Router();
            $route_arr = self::getConfig()->get('url.route');
            foreach ($route_arr as $k => $v) {
                $route = new \Hiano\Route\Route($v['url'], $v['parameter'], $v['requirement'], $v['type']);
                self::$router->addRoute($k, $route);
            }
        }
        return self::$router;
    }
    
    /**
     * 
     * @return \Hiano\View\View
     */
    static function newView(){
        $view = new \Hiano\View\View();
        $type = self::getConfig()->get('view.engine');
        $driver = \Hiano\Hiano::loadDriver('View\\' . $type . 'Driver');
        $view->setDriver($driver);
        if($default_version = self::getConfig()->get('view.default_version')){
            $view->setVersion($default_version);
        }
        return $view;
    }
    
}
