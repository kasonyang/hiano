<?php

/**
 * 
 * @author Kason Yang <i@kasonyang.com>
 */

namespace Hiano;

class ValidateException extends \Exception{
    
}

class ErrorException extends \Exception{
    
}

class RedirectException extends \Exception {
    
    private $redirect_url;
    
    public function __construct($redirect_url,$message = null, $code = null, $previous=null) {
        parent::__construct($message, $code, $previous);
        $this->redirect_url = $redirect_url;
    }
    
    function getRedirectUrl(){
        return $this->redirect_url;
    }
}

class Exception extends \Exception{
    static function validateFailed($field,$value,$validator_name){
        throw new ValidateException("Failed to validate the value of {$field}:{$value},'{$validator_name}' required");
    }
    
    static function error($error_description,$error_code){
        throw new ErrorException($error_description,$error_code);
    }
}