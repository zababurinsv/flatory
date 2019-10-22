<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * migration
 *
 * @date 14.04.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Migration_St_Files_final extends CI_Migration {
    
    private $db_name = '';

    public function __construct($config = array()) {
        $this->db_name = $this->db->database;
    }
    
    public function up() {

        $this->db->query("ALTER TABLE ". $this->db->dbprefix ."file_categories_proportions 
                ADD COLUMN `is_water_mark` TINYINT NOT NULL DEFAULT 0 AFTER `proportion_id`;");

        $this->db->query("ALTER TABLE `". $this->db->dbprefix ."file_involves` CHANGE COLUMN `file_involve_id` `file_involve_id` INT(11) NOT NULL AUTO_INCREMENT;");
        $this->db->query("ALTER TABLE `". $this->db->dbprefix ."storage_files` CHANGE COLUMN `name` `name` VARCHAR(255) NOT NULL;");
        $this->db->query("ALTER TABLE `". $this->db->dbprefix ."file_categories_proportions` CHANGE COLUMN `is_water_mark` `is_watermark` TINYINT(4) NOT NULL DEFAULT '0' ;");
    }
    
    public function down() {
        $this->db->query("ALTER TABLE " . $this->db->dbprefix . "file_categories_proportions DROP COLUMN is_water_mark;");
    }
}
