<?php

/**
 * 
 * @author kasonyang <i@kasonyang.com>
 */

namespace Hiano\Driver\Cache;

class NoCacheDriver implements \Hiano\Cache\CacheDriverInterface {

    private $vars;

    function get($key) {
        return $this->vars[$key];
    }

    function set($key, $value, $ttl = null) {
        $this->vars[$key] = $value;
        return true;
    }

    function exist($key) {
        return isset($this->vars[$key]);
    }

    function delete($key) {
        unset($this->vars[$key]);
        return TRUE;
    }

    function clear() {
        $this->vars = NULL;
        return true;
    }

}
