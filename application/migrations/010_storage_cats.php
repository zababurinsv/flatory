<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * migration
 *
 * @date 08.05.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Migration_Storage_cats extends CI_Migration {
    
    private $db_name = '';

    public function __construct($config = array()) {
        $this->db_name = $this->db->database;
    }
    
    public function up() {
        
        $this->db->query("INSERT INTO " . $this->db->dbprefix . "file_categories VALUES('10', 'Карточка объекта', 'cart', '/catalog/', '/admin/cart/{id}', NULL, '', '1')");
        $this->db->query("INSERT INTO " . $this->db->dbprefix . "file_categories_proportions VALUES(5,1,0)");
        $this->db->query("INSERT INTO " . $this->db->dbprefix . "file_categories_proportions VALUES(5,3,0)");
        $this->db->query("INSERT INTO " . $this->db->dbprefix . "file_categories_proportions VALUES(6,1,0)");
        $this->db->query("INSERT INTO " . $this->db->dbprefix . "file_categories_proportions VALUES(6,3,0)");

    }
    
    public function down() {
        $this->db->query("DELETE FROM " . $this->db->dbprefix . "file_categories where file_category_id = 10");
        
        $this->db->query("DELETE FROM " . $this->db->dbprefix . "file_categories_proportions where file_category_id = 5");
        $this->db->query("DELETE FROM " . $this->db->dbprefix . "file_categories_proportions where file_category_id = 6");
        
    }
}
