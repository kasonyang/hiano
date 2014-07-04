<?php

/**
 * 
 * @author kasonyang <i@kasonyang.com>
 */

namespace Hiano\Session;

interface SessionDriverInterface{
    function open($savePath,$sessionName);
    function close();
    function read($sessionId);
    function write($sessionId,$data);
    function destroy($sessionId);
    function gc($lifetime);
}

class SessionFile{
    private $session;
    /**
     * 清除所有SessionFile
     * @return boolean
     */
    static function clear(){
        $ret = TRUE;
        if($sessions = $_SESSION['_Hiano_Session']['SessionFile']){
            foreach($sessions as $s_name => $s_value){
                $sf = new self($s_name);
                if(!$sf->delete()){
                    $ret = false;
                }
            }
        }
        return $ret;
    }
    function __construct($name) {
        $this->session = & $_SESSION['_Hiano_Session']['SessionFile'][$name];
    }
    /**
     * 绑定文件
     * @param string $filename
     * @return void
     */
    function bind($filename){
        $this->session = $filename;
    }
    /**
     * 解除绑定
     * @return void
     */
    function unbind(){
        unset($this->session);
    }
    /**
     * 是否已绑定文件
     * @return boolean
     */
    function bound(){
        return isset($this->session);
    }
    /**
     * 返回已经绑定的文件名
     * @return string
     */
    function getBoundFile(){
        return $this->session;
    }
    /**
     * 解除绑定,并删除绑定的文件
     * @return boolean
     */
    function delete(){
        if(@unlink($this->session)){
            $this->unbind();
            return TRUE;
        }else{
            return false;
        }
        
    }
}

class SessionManager{
    static function register($session_driver){
        session_set_save_handler(
            array($session_driver, 'open'),
            array($session_driver, 'close'),
            array($session_driver, 'read'),
            array($session_driver, 'write'),
            array($session_driver, 'destroy'),
            array($session_driver, 'gc')
        );
    }
}