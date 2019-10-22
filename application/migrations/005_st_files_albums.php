<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * migration
 *
 * @date 22.04.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Migration_St_Files_albums extends CI_Migration {
    
    private $db_name = '';

    public function __construct($config = array()) {
        $this->db_name = $this->db->database;
    }
    
    public function up() {
        $this->db->query("ALTER TABLE `". $this->db->dbprefix ."image_albums` ADD COLUMN `object_id` INT NOT NULL DEFAULT 0 AFTER `image_album_id`;");
        $this->db->query("ALTER TABLE `". $this->db->dbprefix ."files_image_albums` ADD COLUMN `sort` INT NOT NULL DEFAULT 99 AFTER `image_album_id`;");
        $this->db->query("ALTER TABLE `". $this->db->dbprefix ."file_involves` ADD COLUMN `sort` INT NOT NULL DEFAULT 99 AFTER `parent_alias`;");
        $this->db->query("ALTER TABLE `". $this->db->dbprefix ."image_albums` ADD COLUMN `file_category_id` INT NOT NULL DEFAULT 0 AFTER `image_album_id`;");
        $this->db->query("INSERT INTO `". $this->db->dbprefix ."file_formats` (`name`, `ext`, `status`, `file_type_id`) VALUES ('gif', 'gif', '1', '1');");
       
    }
    
    public function down() {
        $this->db->query("ALTER TABLE " . $this->db->dbprefix . "image_albums DROP COLUMN object_id;");
        $this->db->query("ALTER TABLE " . $this->db->dbprefix . "files_image_albums DROP COLUMN sort;");
        $this->db->query("ALTER TABLE " . $this->db->dbprefix . "file_involves DROP COLUMN sort;");
        $this->db->query("ALTER TABLE " . $this->db->dbprefix . "image_albums DROP COLUMN file_category_id;");
    }
}
