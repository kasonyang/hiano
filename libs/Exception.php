<?php

/**
 * 
 * @author Kason Yang <i@kasonyang.com>
 */

namespace Hiano;

class ValidateException extends \Exception{
    
}

class Exception extends \Exception{
    static function validateFailed($field,$value,$validator_name){
        throw new ValidateException("Failed to validate the value of {$field}:{$value},'{$validator_name}' required");
    }
}