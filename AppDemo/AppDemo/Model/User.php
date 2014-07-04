<?php

/**
 * 
 * @author kasonyang <i@kasonyang.com>
 */

namespace AppDemo\Model;

/**
 * @table user User
 */
class User{
    /**
     *
     * @field integer
     * @var int
     */
    public $id;
    
    /**
     * @field string(length=100)
     */
    public $name;
    
}