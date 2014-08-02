<?php

/**
 * 
 * @version 1.0
 * @author Kason Yang <i@kasonyang.com>
 */

namespace Hiano\Validator;

class EmailValidator implements ValidatorInterface {
    public function validate($value) {
        return preg_match("/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/", $value) ? TRUE : FALSE;
    }
}
