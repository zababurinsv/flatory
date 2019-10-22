<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * migration
 * glossary & handbks
 * @date 03.09.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Migration_Handbk_glossary extends CI_Migration {

    public function __construct($config = array()) {
        
    }

    public function up() {
        
        $index_mark = time();
        
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . $this->db->dbprefix . "handbks` (
                            `handbk_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                            `name` VARCHAR(255) NOT NULL,
                            `alias` VARCHAR(255) NOT NULL,
                            `table` VARCHAR(155) NOT NULL,
                            `description` TEXT NULL,
                            `status` TINYINT NOT NULL DEFAULT 1,
                            PRIMARY KEY (`handbk_id`))
                          ENGINE = InnoDB");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . $this->db->dbprefix . "glossary` (
                            `glossary_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                            `name` VARCHAR(255) NOT NULL,
                            `description` TEXT NOT NULL,
                            `alias` VARCHAR(255) NOT NULL,
                            `parent_id` INT NOT NULL DEFAULT 0,
                            `object_id` INT NOT NULL DEFAULT 0,
                            `handbk_id` INT UNSIGNED NULL,
                            `meta_title` TEXT NULL,
                            `meta_keywords` TEXT NULL,
                            `meta_description` TEXT NULL,
                            `updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                            `created` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
                            `status` TINYINT NOT NULL DEFAULT 1,
                            PRIMARY KEY (`glossary_id`),
                            INDEX `fk_glossary_handbks{$index_mark}_idx` (`handbk_id` ASC),
                            CONSTRAINT `fk_glossary_handbks{$index_mark}`
                              FOREIGN KEY (`handbk_id`)
                              REFERENCES `" . $this->db->dbprefix . "handbks` (`handbk_id`)
                              ON DELETE SET NULL
                              ON UPDATE NO ACTION)
                          ENGINE = InnoDB");
                            
        $this->db->query("ALTER TABLE `" . $this->db->dbprefix . "type_of_building` 
                            ADD COLUMN `status` TINYINT NOT NULL DEFAULT 1 AFTER `name`;");
        $this->db->query("ALTER TABLE `" . $this->db->dbprefix . "building_lot` 
                            ADD COLUMN `status` TINYINT NOT NULL DEFAULT 1 AFTER `name`;");
                            
        $this->db->query("INSERT INTO `" . $this->db->dbprefix . "file_categories` (`name`, `prefix`, `uri`, `uri_adm`, `parent_table`) "
                . "VALUES ('Картотека', 'glossary', '/kartoteka/{alias}', '/admin/glossary/edit/{id}', 'glossary');");
        
        $this->db->query("INSERT INTO `" . $this->db->dbprefix . "file_categories_proportions` (`file_category_id`, `proportion_id`) "
                . "VALUES ((SELECT file_category_id FROM " . $this->db->dbprefix . "file_categories WHERE prefix = 'glossary'), "
                . "(SELECT proportion_id FROM " . $this->db->dbprefix . "proportions WHERE name = '1140x730'));");
        $this->db->query("INSERT INTO `" . $this->db->dbprefix . "file_categories_proportions` (`file_category_id`, `proportion_id`) "
                . "VALUES ((SELECT file_category_id FROM " . $this->db->dbprefix . "file_categories WHERE prefix = 'glossary'), "
                . "(SELECT proportion_id FROM " . $this->db->dbprefix . "proportions WHERE name = '570x380'));");
        
        $this->db->query("INSERT INTO `" . $this->db->dbprefix . "handbks` (`name`, `table`) "
                . "VALUES ('Тип здания', 'type_of_building');");
        $this->db->query("INSERT INTO `" . $this->db->dbprefix . "handbks` (`name`, `table`) "
                . "VALUES ('Серия здания', 'building_lot');");
        $this->db->query("INSERT INTO `" . $this->db->dbprefix . "handbks` (`name`, `table`) "
                . "VALUES ('Картотека', 'glossary');");
        
        
    }

    public function down() {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");
        $this->db->query("DROP TABLE IF EXISTS `" . $this->db->dbprefix . "glossary`;");
        $this->db->query("DROP TABLE IF EXISTS `" . $this->db->dbprefix . "handbks`;");
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
        
        $this->db->query("ALTER TABLE " . $this->db->dbprefix . "type_of_building DROP COLUMN status;");
        $this->db->query("ALTER TABLE " . $this->db->dbprefix . "building_lot DROP COLUMN status;");
        
        $this->db->query("DELETE FROM `" . $this->db->dbprefix . "file_categories_proportions` "
                . "WHERE `file_category_id` IN (SELECT file_category_id FROM " . $this->db->dbprefix . "file_categories WHERE prefix = 'glossary');");
        $this->db->query("DELETE FROM `" . $this->db->dbprefix . "file_categories` WHERE `prefix` = 'glossary';");
        
    }
    

}
