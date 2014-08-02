<?php

/**
 * 
 * @version 1.0
 * @author Kason Yang <i@kasonyang.com>
 */

namespace Hiano\Validator;

class IntegerValidator implements ValidatorInterface {
    public function validate($value) {
        return preg_match('/^[0-9]+$/', $value) ? TRUE : FALSE;
    }
}
