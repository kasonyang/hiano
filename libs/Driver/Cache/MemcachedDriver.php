<?php

/**
 * 
 * @author kasonyang <i@kasonyang.com>
 */

namespace Hiano\Driver\Cache;

class MemcachedDriverException {
    
}

class MemcachedDriver implements \Hiano\Cache\CacheDriverInterface {

    private function getMemcached() {
        static $memcached = null;
        if (!$memcached) {
            $mc = new Memcached();
            if (!$mc->addServer(\Hiano\Config\Config::get('memcached.host'), \Hiano\Config\Config::get('memcached.port'))) {
                throw new MemcachedDriverException('Failed to Connect the Memcached Server');
            }
            $memcached = $mc;
        }
        return $memcached;
    }

    function get($key) {
        return $this->getMemcached()->get($key);
    }

    function set($key, $value, $ttl = null) {
        return $this->getMemcached()->set($key, $value, $ttl);
    }

    function exist($key) {
        $this->getMemcached()->get($key);
        return $this->getMemcached()->getResultCode() !== Memcached::RES_NOTFOUND;
    }

    function delete($key) {
        return $this->getMemcached()->delete($key);
    }

    function clear() {
        return $this->getMemcached()->flush();
    }

}
