<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * migration
 *
 * @date 12.03.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Migration_St_Files extends CI_Migration {
    
    private $db_name = '';

    public function __construct($config = array()) {
        $this->db_name = $this->db->database;
    }
    
    public function up() {
        $this->db->query("ALTER TABLE ". $this->db->dbprefix ."file_types 
                ADD COLUMN `alias` VARCHAR(155) NOT NULL AFTER `description`;");

        $this->db->query("UPDATE ". $this->db->dbprefix ."file_types SET `alias`='images' WHERE `file_type_id`='1';");
        $this->db->query("UPDATE ". $this->db->dbprefix ."file_types SET `alias`='docs' WHERE `file_type_id`='2';");

        $this->db->query("ALTER TABLE `". $this->db->dbprefix ."storage_files` 
            ADD COLUMN `x` INT NOT NULL DEFAULT 0 AFTER `autor`,
            ADD COLUMN `y` INT NOT NULL DEFAULT 0 AFTER `x`;");

        $this->db->query("ALTER TABLE `". $this->db->dbprefix ."storage_files` 
            CHANGE COLUMN `autor` `alt` TEXT NULL DEFAULT NULL ;");

        $this->db->query("ALTER TABLE `". $this->db->dbprefix ."files_proportions` 
            CHANGE COLUMN `is_water_mark` `is_watermark` TINYINT(4) NOT NULL DEFAULT '0';");
    }
    
    public function down() {
        $this->db->query("ALTER TABLE " . $this->db->dbprefix . "file_types DROP COLUMN alias;");
        
        $this->db->query("ALTER TABLE " . $this->db->dbprefix . "storage_files DROP COLUMN x;");
        $this->db->query("ALTER TABLE " . $this->db->dbprefix . "storage_files DROP COLUMN y;");
        
        $this->db->query("ALTER TABLE `". $this->db->dbprefix ."storage_files` 
            CHANGE COLUMN `alt` `autor` TEXT NULL DEFAULT NULL ;");
        
        $this->db->query("ALTER TABLE `". $this->db->dbprefix ."files_proportions` 
            CHANGE COLUMN `is_watermark` `is_water_mark` TINYINT(4) NOT NULL DEFAULT '0';");
    }
}
