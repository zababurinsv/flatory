<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * populated_locality_type
 *
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Populated_Locality_Type_Model extends MY_Model {
    
    public function __construct() {
        parent::__construct();
        
        $this->table = 'populated_locality_type';
        $this->primary_key = 'populated_locality_type_id';
    }
    
}
