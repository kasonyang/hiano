<?php

/**
 * 
 * @author kasonyang <i@kasonyang.com>
 */

namespace Hiano\Token;

class Token{
    /**
     * 产生新的Token
     * @param string $name Token关键字
     * @return string 生成的Token
     */
    static function generate($name){
        $sid = session_id();
        if(empty($sid)){
            session_start ();
        }
        return $_SESSION['_hiano_token'][$name] = uniqid() . rand(0,9999);
    }
    
    /**
     * 验证Token是否正确
     * @param string $name Token关键字
     * @param string $token 要验证的Token值
     * @return boolean
     */
    static function validate($name,$token){
        if(isset($_SESSION['_hiano_token'][$name])){
            $ret =  $_SESSION['_hiano_token'][$name] == $token;
            unset($_SESSION['_hiano_token'][$name]);
            return $ret;
        }
        return false;
    }
}
