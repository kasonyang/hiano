<?php

/**
 * 
 * @author kasonyang <i@kasonyang.com>
 */

namespace Hiano;

include_once __DIR__ . '/App.php';
include_once __DIR__ . '/Cache.php';
include_once __DIR__ . '/Config.php';
include_once __DIR__ . '/Controller.php';
include_once __DIR__ . '/Filter.php';
include_once __DIR__ . '/Request.php';
include_once __DIR__ . '/Route.php';
include_once __DIR__ . '/Session.php';
include_once __DIR__ . '/Token.php';
include_once __DIR__ . '/View.php';
include_once __DIR__ . '/Widget.php';

class FileNotExistException extends \Exception {
    
}

class ClassLoader {

    static function load($class) {
        $class_file_name = str_replace('\\', '/', $class) . '.php';
        try {
            $dir = HIANO_APP_PATH;
            $app_name = App\App::getName();
            $namespace = explode('\\', $class);
            if ($namespace[0] == $app_name) {
                \Hiano\Hiano::includeFile($dir . '/../', $class_file_name);
            }
        } catch (FileNotExistException $e) {
            
        }
    }
    
}

class Hiano {

    /**
     * 包含文件
     * @param string $paths 以分号为分隔符的路径
     * @param string $file_name 文件名
     * @return mixed 同include返回值
     * @throws FileNotExistException
     */
    static function includeFile($paths, $file_name) {
        $path_arr = explode(';', $paths);
        foreach ($path_arr as $p) {
            $file = $p . '/' . $file_name;
            if (file_exists($file)){
                return include $file;
            }
        }
        throw new FileNotExistException("unable to include the file $file_name");
    }

    /**
     * 加载驱动
     * @param string $driver_name 驱动名称
     * @param mixed $params 实例化驱动的参数
     * @return object 驱动实例
     * @throws \Exception
     */
    static function loadDriver($driver_name, $params = NULL) {
        $driver_class = '\\Hiano\\Driver\\' . $driver_name;
        if (!class_exists($driver_class)) {
                throw new \Exception('无法装载驱动，驱动文件不存在！');
        }
        if ($params) {
            return new $driver_class($params);
        } else {
            return new $driver_class;
        }
    }

    /**
     * 包含文件
     * @param string $name 文件名，不包含".php"
     * @return mixed 文件返回值
     */
    static function import($name) {
        $file_name = $name . '.php';
        return self::includeFile(App\App::getImportPath(), $file_name);
    }

    static function init() {
        if (!defined('HIANO_APP_PATH')){
            die('ERROR:HIANO_APP_PATH undefined!');
        }

        spl_autoload_register(array('Hiano\ClassLoader','load'));

        if (App\App::getConfig()->get('debug.enable', false)) {
            $debug_config = App\App::getConfig()->get('debug');
            error_reporting($debug_config['error_reporting']);
            unset($debug_config);
        } else {
            error_reporting(0);
        }

        if (App\App::getConfig()->get('timezone')) {
            date_default_timezone_set(App\App::getConfig()->get('timezone'));
        }



        if($session_path = App\App::getConfig()->get('session_path')){
            //todo load and register SessionDriver
            //$session_driver = Hiano::loadDriver('\\Hiano\\')
            //\Hiano\Session\SessionManager::register();
        }
        
        //\Hiano\App\App::registerModelPath(HIANO_APP_PATH . '/widgets/models');

        $app_path = realpath(HIANO_APP_PATH);
        \Hiano\App\App::registerModelPath($app_path . '/Model');
        \Hiano\App\App::registerImportPath($app_path . '/Include');
    }

}

Hiano::init();