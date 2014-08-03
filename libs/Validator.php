<?php

/**
 * 
 * @author Kason Yang <i@kasonyang.com>
 */

namespace Hiano\Validator;

interface ValidatorInterface{
    function validate($value);
}

class Validator{
    /**
     * 
     * @param mixed $value
     * @param string $validator_name
     * @return boolean
     */
    static function validate($value,$validator_name){
        $validator = new $validator_name();
        /* @var $validator ValidatorInterface */
        return $validator->validate($value);
    }
}