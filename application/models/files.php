<?php

/**
 * Model files
 *
 * @date 08.03.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Files extends MY_Model {
    
    public function __construct() {
        parent::__construct();
        
        $this->table = 'files';
        $this->primary_key = 'file_id';
    }
    
}
