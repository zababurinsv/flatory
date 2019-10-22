<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Model file types
 *
 * @date 12.03.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class File_Types extends MY_Model {
    
    public function __construct() {
        parent::__construct();
        
        $this->table = 'file_types';
        $this->primary_key = 'file_type_id';
    }
    
}
