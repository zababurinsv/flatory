<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Model links
 *
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Links extends MY_Model {
    
    
    public function __construct() {
        parent::__construct();
        
        $this->table = 'links';
        $this->primary_key = 'link_id';
    }
    
    /**
     * Replace links by object_id
     * @param int $object_id - id of object
     * @param array $links - links array
     */
    public function replace_object_links($object_id, $links) {
        // delete old
        $this->db->delete($this->table, array('object_id' => (int)$object_id));
        // create new
        foreach ($links as $link)
            $this->insert (array(
                'name' =>  element('name', $link),
                'link' =>  element('link', $link),
                'object_id' => (int)$object_id,
            ));
    }
}
