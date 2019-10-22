<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * migration
 *
 * @date 29.03.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Migration_St_Files_edit extends CI_Migration {
    
    private $db_name = '';

    public function __construct($config = array()) {
        $this->db_name = $this->db->database;
    }
    
    public function up() {
        $this->db->query("ALTER TABLE ". $this->db->dbprefix ."file_involves 
                ADD COLUMN `parent_alias` VARCHAR(255) NULL AFTER `parent_id`;");
    }
    
    public function down() {
        $this->db->query("ALTER TABLE " . $this->db->dbprefix . "file_involves DROP COLUMN parent_alias;");
    }
}
