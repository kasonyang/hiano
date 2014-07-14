<?php

/**
 * 
 * @author kasonyang <i@kasonyang.com>
 */

namespace Hiano\Request;

class Request {

    private $parameter, $post, $cookie , $files ,$server_vars;
    
    function __construct($parameter,$post,$files,$server_vars,$cookie) {
        $this->parameter = $parameter;
        $this->post = $post;
        $this->files = $files;
        $this->server_vars = $server_vars;
        $this->cookie = $cookie;
    }

    private static function getSubArray($arr, $keys = null) {
        $sub = NULL;
        if ($keys) {
            if(is_array($keys)){
                foreach ($keys as $v) {
                    $sub[$v] = $arr[$v];
                }
            }else{
                $sub = $arr[$keys];
            }
        } else {
            $sub = $arr;
        }
        return $sub;
    }

    /**
     * 返回请求URL的参数
     * @param string|array $names 参数名
     * @return string|array $names为字符串时返回单个结果,为数组或null时返回多个结果(数组)
     */
    function getParameter($names = null) {
        return self::getSubArray($this->parameter, $names);
    }

    /**
     * 返回表单的POST数据
     * @param string|array $names 参数名
     * @return string|array $names为字符串时返回单个结果,为数组或null时返回多个结果(数组)
     */
    function getPost($names = null) {
        return self::getSubArray($this->post, $names);
    }

    /**
     * 返回浏览器保存的Cookie
     * @param string|array $names
     * @return string|array $names为字符串时返回单个结果,为数组或null时返回多个结果(数组)
     */
    function getCookie($names = null) {
        return self::getSubArray($this->cookie, $names);
    }

    /**
     * 是否存在参数
     * @param string $name
     * @return bool
     */
    function hasParameter($name /*= null*/) {
        return isset($this->parameter[$name]);
    }

    /**
     * 是否存在指定的POST数据
     * @param string $name
     * @return bool
     */
    function hasPost($name = null) {
        if ($name){
            return isset($this->post[$name]);
        }else{
            return count($this->post) > 0;
        }
    }

    /**
     * 是否存在指定的Cookie
     * @param string $name
     * @return bool
     */
    function hasCookie($name = null) {
        if ($name){
            return isset($this->cookie[$name]);
        }else{
            return count($this->cookie) > 0;
        }
    }

    /**
     * 是否存在指定的POST或GET
     * @param string $name
     * @return boolean
     */
    function hasRequest($name){
        return $this->hasPost($name) or $this->hasParameter($name);
    }
    
    /**
     * 
     * 读取Request（POST或GET）
     * 
     * @param string|array $names
     * @return string|array
     */
    function getRequest($names=NULL){
        if($ret = $this->getPost($names)){
            return $ret;
        }else{
            return $this->getParameter($names);
        }
    }
    
    /**
     * 是否为POST请求
     * @return bool
     */
    function isPost(){
        return $this->server_vars['REQUEST_METHOD'] == "POST";
    }
    
    /**
     * 是否为GET请求
     * @return bool
     */
    function isGet(){
        return $this->server_vars['REQUEST_METHOD'] == 'GET';
    }

}
