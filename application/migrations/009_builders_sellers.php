<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * migration
 *
 * @date 31.05.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Migration_Builders_sellers extends CI_Migration {
    
    private $db_name = '';

    public function __construct($config = array()) {
        $this->db_name = $this->db->database;
    }
    
    public function up() {
        
        $this->db->query("ALTER TABLE `". $this->db->dbprefix ."builders_object` 
            ADD COLUMN `file_id` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `logo`;");

        $this->db->query("ALTER TABLE `". $this->db->dbprefix ."sellers_object` 
            ADD COLUMN `file_id` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `logo`;");
    }
    
    public function down() {
        $this->db->query("ALTER TABLE " . $this->db->dbprefix . "builders_object DROP COLUMN file_id;");
        $this->db->query("ALTER TABLE " . $this->db->dbprefix . "sellers_object DROP COLUMN file_id;");
    }
}
