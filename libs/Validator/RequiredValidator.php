<?php

/**
 * 
 * @author Kason Yang <i@kasonyang.com>
 */
namespace Hiano\Validator;

class RequiredValidator implements ValidatorInterface{
    public function validate($value) {
        return $value == '';
    }
}