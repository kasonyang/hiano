<?php

/**
 * 
 * @author kasonyang <i@kasonyang.com>
 */

namespace Hiano\Config;

class Config {
    /**
     *
     * @var ConfigParser 
     */
    private $config;
    
    private $config_file_path = '';


    function setConfigFilePath($path){
        $this->config_file_path = $path . '/';
    }


    /**
     * 读取配置信息
     * @param string $name 主键，使用'.'分级
     * @param mix $default 默认值，如果没有找到相应的配置，则返回此值
     * @return mix
     */
    function get($name,$default = null){
        $name_arr = explode(':', $name);
        if(!isset($name_arr[1])){
            $name_arr[1] = $name_arr[0];
            $name_arr[0] = 'system';
        }
        if(!isset($this->config[$name_arr[0]])){
            $this->config[$name_arr[0]] = new ConfigParser($this->config_file_path . $name_arr[0] . ".config.php");
        }
        return $this->config[$name_arr[0]]->get($name_arr[1],$default);
    }
}

class ConfigParser {

    private $file_path, $included, $configs;

    function __construct($file_path) {
        $this->file_path = $file_path;
    }

    function get($name = null, $default = null) {
        if (!$this->included) {
            if (file_exists($this->file_path)){
                $this->configs = include $this->file_path;
            }
            $this->included = true;
        }
        if ($name){
            $name_arr = explode('.', $name);
        }
        $name_deep = count($name_arr);
        $config = $this->configs;
        for ($i = 0; $i < $name_deep; $i++) {
            $config = $config[$name_arr[$i]];
            if (!isset($config)){
                return $default;
            }
        }
        return $config;
    }

}
