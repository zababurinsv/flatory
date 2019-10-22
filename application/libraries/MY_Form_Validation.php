<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Extend Form Validation
 * @date 30.07.2014
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class MY_Form_validation extends CI_Form_validation {

    /**
     * Get errors array
     * @return array
     */
    public function get_errors() {
        return $this->_error_array;
    }
    
    /**
     * Rule alias 
     * @param string $str
     * @return bool
     */
    public function alias($str) {
        return $this->regex_match($str, '/^[a-zA-Z0-9-]+$/');
    }

}
