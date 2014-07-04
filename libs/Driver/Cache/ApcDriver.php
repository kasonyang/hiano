<?php

/**
 * 
 * @author kasonyang <i@kasonyang.com>
 */

namespace Hiano\Driver\Cache;

class ApcDriver implements \Hiano\Cache\CacheDriverInterface {

    function get($key) {
        return apc_fetch($key);
    }

    function set($key, $value, $ttl = null) {
        return apc_store($key, $value, $ttl);
    }

    function exist($key) {
        return apc_exists($key);
    }

    function delete($key) {
        return apc_delete($key);
    }

    function clear() {
        return apc_clear_cache();
    }

}
