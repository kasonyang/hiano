<?php

/**
 * 
 * @author kasonyang <i@kasonyang.com>
 */

namespace Hiano\Filter;

interface FilterInterface{
    /**
     * @param FilterChain $filter_chain
     */
    function execute($filter_chain);
}

class FilterChain {

    private $execute_index = 0;
    
    private $filters = [];
    
    private $callback = NULL;
    
    private $callback_params = [];
            
    function __construct($callback = NULL,$params = []) {
        $this->callback = $callback;
        $this->callback_params = $params;
    }

    /**
     * 
     * @param Filter $filter
     */
    function addFilter($filter) {
        $this->filters[] = $filter;
    }

    function execute() {
        $filter_count = count($this->filters);
        if ($this->execute_index < $filter_count) {
            $filter = $this->filters[$this->execute_index];
            $this->execute_index++;
            $filter->execute($this);
        } elseif ($this->execute_index == $filter_count) {
            $this->execute_index++;
            $callback = $this->callback;
            call_user_func_array($callback, $this->callback_params);
        }
    }

}

class Filter {
    
    /**
     * 
     * @param FilterChain $filter_chain
     */
    function execute($filter_chain){
        $filter_chain->execute();
    }
    
}