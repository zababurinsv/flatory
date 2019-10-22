<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * migration
 *
 * @date 03.05.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Migration_St_Files_albums_final extends CI_Migration {
    
    private $db_name = '';

    public function __construct($config = array()) {
        $this->db_name = $this->db->database;
    }
    
    public function up() {
        $this->db->query("DROP TABLE IF EXISTS `". $this->db->dbprefix ."files_image_albums`;");
        $this->db->query("CREATE TABLE `". $this->db->dbprefix ."files_image_albums` (
                    `file_id` INT NOT NULL,
                    `image_album_id` INT NOT NULL,
                    `file_involve_id` INT NOT NULL,
                    `sort` INT NOT NULL DEFAULT 99,
                    PRIMARY KEY (`file_id`, `image_album_id`),
                    INDEX `fk_albums_has_storage_files1_idx` (`file_id` ASC),
                    INDEX `fk_albums_has_files_albums1_idx` (`image_album_id` ASC),
                    INDEX `fk_files_image_albums_file_involves1_idx` (`file_involve_id` ASC),
                    CONSTRAINT `fk_albums_has_files_albums1`
                      FOREIGN KEY (`image_album_id`)
                      REFERENCES `". $this->db->dbprefix ."image_albums` (`image_album_id`)
                      ON DELETE NO ACTION
                      ON UPDATE NO ACTION,
                    CONSTRAINT `fk_albums_has_storage_files1`
                      FOREIGN KEY (`file_id`)
                      REFERENCES `". $this->db->dbprefix ."storage_files` (`file_id`)
                      ON DELETE NO ACTION
                      ON UPDATE NO ACTION,
                    CONSTRAINT `fk_files_image_albums_file_involves1`
                      FOREIGN KEY (`file_involve_id`)
                      REFERENCES `". $this->db->dbprefix ."file_involves` (`file_involve_id`)
                      ON DELETE CASCADE
                      ON UPDATE NO ACTION)
                  ENGINE = InnoDB;");
//        $this->db->query("ALTER TABLE `". $this->db->dbprefix ."image_albums` CHANGE COLUMN `status` `sort` INT(11) NOT NULL DEFAULT '99';");
       
    }
    
    public function down() {
        $this->db->query("ALTER TABLE `". $this->db->dbprefix ."image_albums` CHANGE COLUMN `sort` `status` INT(11) NOT NULL DEFAULT '1';");
    }
}
