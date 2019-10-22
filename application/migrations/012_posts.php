<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * migration
 * posts
 * @date 29.07.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Migration_Posts extends CI_Migration {

    public function __construct($config = array()) {
    }

    public function up() {
        
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . $this->db->dbprefix . "posts` (
                `post_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `file_category_id` INT NOT NULL,
                `name` TEXT NOT NULL,
                `content` TEXT NOT NULL,
                `anons` TEXT NULL,
                `alias` VARCHAR(155) NOT NULL,
                `title` TEXT NULL,
                `keywords` TEXT NULL,
                `description` TEXT NULL,
                `updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                `created` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
                `user_id` INT NOT NULL DEFAULT 0,
                `file_id` INT NOT NULL DEFAULT 0,
                `status` INT NOT NULL DEFAULT 2,
                PRIMARY KEY (`post_id`),
                INDEX `fk_content_file_categories1_idx` (`file_category_id` ASC),
                INDEX `fk_posts_storage_files1_idx` (`file_id` ASC),
                CONSTRAINT `fk_content_file_categories1`
                  FOREIGN KEY (`file_category_id`)
                  REFERENCES `" . $this->db->dbprefix . "file_categories` (`file_category_id`)
                  ON DELETE CASCADE
                  ON UPDATE NO ACTION,
                CONSTRAINT `fk_posts_storage_files1`
                  FOREIGN KEY (`file_id`)
                  REFERENCES `" . $this->db->dbprefix . "storage_files` (`file_id`)
                  ON DELETE RESTRICT
                  ON UPDATE NO ACTION)
              ENGINE = InnoDB;");
        
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . $this->db->dbprefix . "posts_tags` (
            `post_id` INT UNSIGNED NOT NULL,
            `tag_id` INT NOT NULL,
            PRIMARY KEY (`post_id`, `tag_id`),
            INDEX `fk_content_has_tags_tags1_idx` (`tag_id` ASC),
            INDEX `fk_content_has_tags_content1_idx` (`post_id` ASC),
            CONSTRAINT `fk_content_has_tags_content1`
              FOREIGN KEY (`post_id`)
              REFERENCES `" . $this->db->dbprefix . "posts` (`post_id`)
              ON DELETE CASCADE
              ON UPDATE NO ACTION,
            CONSTRAINT `fk_content_has_tags_tags1`
              FOREIGN KEY (`tag_id`)
              REFERENCES `" . $this->db->dbprefix . "tags` (`tag_id`)
              ON DELETE CASCADE
              ON UPDATE NO ACTION)
          ENGINE = InnoDB;");
        
        $this->db->query("ALTER TABLE `" . $this->db->dbprefix . "tags` 
            CHANGE COLUMN `description` `alias` VARCHAR(155) NOT NULL ;");
    }

    public function down() {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");
        $this->db->query("DROP TABLE IF EXISTS `" . $this->db->dbprefix . "posts`;");
        $this->db->query("DROP TABLE IF EXISTS `" . $this->db->dbprefix . "posts_tags`;");
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
        
        $this->db->query("ALTER TABLE `" . $this->db->dbprefix . "tags` 
            CHANGE COLUMN `alias` `description` VARCHAR(155) NOT NULL ;");
    }

}
