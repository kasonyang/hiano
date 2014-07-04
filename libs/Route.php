<?php

/**
 * 
 * @author kasonyang <i@kasonyang.com>
 */

namespace Hiano\Route;

class Router {

    /**
     *
     * @var spRoute route
     */
    private $route;
    
    private $suffix;

    function setSuffix($suffix){
        $this->suffix = $suffix;
    }
    
    function getSuffix(){
        return $this->suffix;
    }
    
    function addRoute($name,$route) {
        $this->route[$name] = $route;
    }

    /**
     * 
     * @param type $main_url
     * @return boolean|array
     */
    private function parseMainUrl($main_url){
        if ($this->route) {
            foreach ($this->route as $route) {
                $params = $route->parse($main_url);
                if ($params !== FALSE){
                    return $params;
                }
            }
        }
        return false;
    }
    
    /**
     * 
     * @param string $url 外部url
     * @return boolean|array
     */
    function parse($url) {
        $url_arr = explode('?', $url);     
        $main_url = $url_arr[0];
        if($this->suffix and substr($main_url,-strlen($this->suffix)) == $this->suffix){
            $main_url = substr($main_url, 0, -strlen($this->suffix));
        }
        $params = $this->parseMainUrl($main_url);
        if ($params === false){
            throw new UrlParseException('无法解析的URL:' . $main_url);
        } 
        foreach ($params as $key => $value) {
            $params[$key] = urldecode($value);
        }
        return $params;
    }
    
    /**
     * 
     * @param array $params
     * @return boolean
     * @throws UrlFormatException
     */
    function format($params) {
        if ($this->route) {
            foreach ($this->route as $route) {
                if ($url = $route->format($params)){
                    return \Hiano\App\App::getBaseUrl() . $url . $this->getSuffix();
                }
            }
        }
        throw new UrlFormatException('无法格式化URL:' . print_r($arr, TRUE));
    }

}

class Route {

    const TYPE_STATIC = 0;
    const TYPE_DYMATIC = 1;

    private $delimiter = '/';
    private $parts = [], $names;
    private $params, $requirements, $type;

    function __construct($url, $params = null, $requirements = null, $type = self::TYPE_STATIC) {
        $this->params = $params;
        $this->type = $type;
        $this->requirements = $requirements;
        if($url){
            $url_arr = explode($this->delimiter, $url);
            foreach ($url_arr as $k => $v) {
                if (substr($v, 0, 1) === ':') {
                    $this->parts[$k] = null;
                    $this->names[$k] = substr($v, 1);
                } else {
                    $this->parts[$k] = $v;
                }
            }
        }
    }

    /**
     * 
     * @param string $url 外部url
     * @return boolean
     */
    function parse($url) {
        $parts = array();
        $vars = array();
        $params = array();
        $url_parts = explode($this->delimiter, trim($url, $this->delimiter));
        foreach ($url_parts as $k => $v) {
            if ($v != ''){
                $parts[] = $v;
            }
        }
        $parts_count = count($parts);
        $route_parts_count = count($this->parts);
        if ($parts_count < $route_parts_count){
            return false;
        }
        if ($this->type == self::TYPE_STATIC) {
            if ($parts_count != $route_parts_count){
                return FALSE;
            }
        }
        foreach ($parts as $k => $v) {
            if (isset($this->names[$k])) {
                $vars[$this->names[$k]] = $v;
            } elseif (isset($this->parts[$k])) {
                if ($this->parts[$k] !== $v){
                    return false;
                }
            }else {
                if ($this->type == self::TYPE_STATIC){
                    return false;
                }
                $params[] = $v;
            }
        }
        if ($this->requirements) {
            foreach ($this->names as $k => $v) {
                if ($this->requirements[$v]) {
                    if (!preg_match('/^' . $this->requirements[$v] . '$/', $parts[$k])){
                        return false;
                    }
                }
            }
        }
        $params_count = count($params);
        for ($i = 0; $i < $params_count; $i+=2) {
            $vars[$params[$i]] = $params[$i + 1];
        }
        if ($this->params) {
            foreach ($this->params as $k => $v) {
                $vars[$k] = $v;
            }
        }
        return $vars;
    }

    function format($params) {
        $keys = $this->names;
        if ($this->params) {
            if ($keys) {
                $keys = array_merge($keys, array_keys($this->params));
            } else {
                $keys = array_keys($this->params);
            }
        }
        if ($this->type == 0) {
            if (count($keys) != count($params)){
                return false;
            }
            foreach ($keys as $k) {
                if (!array_key_exists($k, $params)){
                    return false;
                }       
            }
        }
        if ($this->requirements) {
            foreach ($this->names as $k => $v) {
                if ($this->requirements[$v]) {
                    if (!preg_match('/^' . $this->requirements[$v] . '$/', $params[$v])){
                        return false;
                    }
                }
            }
        }
        if ($this->params) {
            foreach ($this->params as $k => $v) {
                if ($params[$k] !== $v){
                    return false;
                }
                unset($params[$k]);
            }
        }
        foreach ($this->parts as $k => $v) {
            if (isset($this->names[$k])) {
                $parts[$k] = $params[$this->names[$k]];
                unset($params[$this->names[$k]]);
            } else {
                $parts[$k] = $this->parts[$k];
            }
        }
        if ($params) {
            foreach ($params as $k => $v) {
                $parts[] = urlencode($k);
                $parts[] = urlencode($v);
            }
        }
        return implode($this->delimiter, $parts);
    }

}

class UrlParseException extends \Exception {
    
}

class UrlFormatException extends \Exception {
    
}

class StandardUrl {

    private $main_url, $querys;

    static function query2array($query) {
        $querys = explode('&', $query);
        foreach ($querys as $q) {
            $arr = explode('=', $q);
            if ($arr[0] != ''){
                $ret[$arr[0]] = isset($arr[1]) ? $arr[1] : '';
            }
        }
        return $ret;
    }

    static function array2query($arr) {
        $query = '';
        foreach ($arr as $k => $v) {
            if ($query){
                $query.='&';
            }
            $query.=urlencode($k) . '=' . urlencode($v);
        }
        return $query;
    }

    function __construct($url) {
        $url_arr = explode('?', $url);
        $this->main_url = $url_arr[0];
        if (isset($url_arr[1])) {
            $this->querys = self::query2array($url_arr[1]);
        }
    }

    function getQuery($name = null) {
        if (isset($name)){
            return $this->querys[$name];
        }else{
            return $this->querys;
        }
    }

    function setQuery($name, $value) {
        $this->querys[$name] = $value;
    }

    function build() {
        return $this->main_url . '?' . self::array2query($this->querys);
    }

    function __toString() {
        return $this->build();
    }

}

class InnerUrl{
    
    private $default_module,$default_controller,$default_action;
            
    function __construct($default_module,$default_controller,$default_action) {
        $this->default_module = $default_module;
        $this->default_controller = $default_controller;
        $this->default_action = $default_action;
    }
    
    function parse($inner_url){
        $url_arr = explode('?', $inner_url);
        $c_n_a = explode('/', $url_arr[0]);
        switch (count($c_n_a)) {
            case 3:
                $ret['module'] = $c_n_a[0];
                $ret['controller'] = $c_n_a[1];
                $ret['action'] = $c_n_a[2];
                break;
            case 2:
                $ret['module'] = $this->default_module;
                $ret['controller'] = $c_n_a[0];
                $ret['action'] = $c_n_a[1];
                break;
            case 1:
                $ret['module'] = $this->default_module;
                $ret['controller'] = $this->default_controller;
                $ret['action'] = $c_n_a[0] == '' ? $this->default_action : $c_n_a[0];
        }
        if (isset($url_arr[1])) {
            $querys = StandardUrl::query2array($url_arr[1]);
            $ret = array_merge($ret, $querys);
        }
        return $ret;
    }
    
}
