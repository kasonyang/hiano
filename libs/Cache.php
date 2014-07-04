<?php

/**
 * 
 * @author kasonyang <i@kasonyang.com>
 */

namespace Hiano\Cache;

interface CacheDriverInterface{
    function get($key);
    /**
     * 
     * @param string $key
     * @param mixed $value
     * @param integer $ttl
     * @return bool
     */
    function set($key,$value,$ttl=null);
    function exist($key);
    /**
     * 
     * @return bool
     */
    function delete($key);
    /**
     * @return bool Description
     */
    function clear();
}

class Cache implements CacheDriverInterface{
    /**
     *
     * @var CacheDriverInterface
     */
    private $driver;
    
    private static $default_type = NULL;
    
    static function getDefaultType(){
        return self::$default_type;
    }
    
    static function setDefaultType($type){
        self::$default_type = $type;
    }


    /**
     * 返回缓存示例
     * @staticvar null $instances
     * @param string $type 缓存类型
     * @return Cache
     */
    static function getInstance($type = null){
        static $instances = null;
        if(!$type){
            $type = self::getDefaultType();
        }
        if(!isset($instances[$type])){
            $driver_type =  $type ;
            $instances[$type] = new self($driver_type);
        }
        return $instances[$type];
    }

    private function __construct($type) {
        $drive_name = $type . 'Driver';
        $this->driver = \Hiano\Hiano::loadDriver($drive_name);
    }
    
    /**
     * 读取数据
     * @param string $key 主键
     * @return mix
     */
    function get($key){
        return $this->driver->get($key);
    }
    
    /**
     * 保存数据
     * @param string $key
     * @param mix $value
     * @param int $ttl
     * @return bool
     */
    function set($key, $value, $ttl = null) {
        return $this->driver->set($key, $value, $ttl);
    }
    
    /**
     * 检查是否已有数据
     * @param string $key 主键
     * @return bool
     */
    function exist($key) {
        return $this->driver->exist($key);
    }
    
    /**
     * 清空数据
     * @return bool
     */
    function clear() {
        return $this->driver->clear();
    }
    
    /**
     * 删除数据
     * @param type $key 主键
     * @return bool
     */
    function delete($key) {
        return $this->driver->delete($key);
    }
}
