<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * migration
 * organisations
 * @date 08.10.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Migration_Organisations extends CI_Migration {

    public function __construct($config = array()) {
        
    }

    public function up() {
        $index_mark = time();
        
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . $this->db->dbprefix . "organizations` (
                        `organization_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                        `name` VARCHAR(255) NOT NULL,
                        `alias` VARCHAR(255) NOT NULL,
                        `params` TEXT NOT NULL,
                        `description` TEXT NULL,
                        `file_id` INT NULL,
                        `meta_title` TEXT NULL,
                        `meta_keywords` TEXT NULL,
                        `meta_description` TEXT NULL,
                        `created` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
                        `updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        PRIMARY KEY (`organization_id`),
                        INDEX `fk_organizations_files{$index_mark}_idx` (`file_id` ASC),
                        CONSTRAINT `fk_organizations_files{$index_mark}`
                          FOREIGN KEY (`file_id`)
                          REFERENCES `" . $this->db->dbprefix . "storage_files` (`file_id`)
                          ON DELETE SET NULL
                          ON UPDATE NO ACTION)
                      ENGINE = InnoDB
                      DEFAULT CHARACTER SET = utf8
                      COLLATE = utf8_general_ci;");

        
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . $this->db->dbprefix . "organizations_tags` (
                        `tag_id` INT NOT NULL,
                        `organization_id` INT UNSIGNED NOT NULL,
                        PRIMARY KEY (`tag_id`, `organization_id`),
                        INDEX `fk_tags_has_organizations_organizations{$index_mark}_idx` (`organization_id` ASC),
                        INDEX `fk_tags_has_organizations_tags{$index_mark}_idx` (`tag_id` ASC),
                        CONSTRAINT `fk_tags_has_organizations_tags{$index_mark}`
                          FOREIGN KEY (`tag_id`)
                          REFERENCES `" . $this->db->dbprefix . "tags` (`tag_id`)
                          ON DELETE CASCADE
                          ON UPDATE NO ACTION,
                        CONSTRAINT `fk_tags_has_organizations_organizations{$index_mark}`
                          FOREIGN KEY (`organization_id`)
                          REFERENCES `" . $this->db->dbprefix . "organizations` (`organization_id`)
                          ON DELETE CASCADE
                          ON UPDATE NO ACTION)
                      ENGINE = InnoDB
                      DEFAULT CHARACTER SET = utf8
                      COLLATE = utf8_general_ci;");
                        
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . $this->db->dbprefix . "organization_types` (
                        `organization_type_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                        `name` VARCHAR(45) NOT NULL,
                        `params` TEXT NOT NULL,
                        PRIMARY KEY (`organization_type_id`))
                      ENGINE = InnoDB
                      DEFAULT CHARACTER SET = utf8
                      COLLATE = utf8_general_ci;");
                        
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . $this->db->dbprefix . "organizations_organization_types` (
                    `organization_type_id` INT UNSIGNED NOT NULL,
                    `organization_id` INT UNSIGNED NOT NULL,
                    PRIMARY KEY (`organization_type_id`, `organization_id`),
                    INDEX `fk_organization_types_has_o{$index_mark}_idx` (`organization_id` ASC),
                    INDEX `fk_organization_types_has_o_types{$index_mark}_idx` (`organization_type_id` ASC),
                    CONSTRAINT `fk_organization_types_has_o_types{$index_mark}`
                      FOREIGN KEY (`organization_type_id`)
                      REFERENCES `" . $this->db->dbprefix . "organization_types` (`organization_type_id`)
                      ON DELETE CASCADE
                      ON UPDATE NO ACTION,
                    CONSTRAINT `fk_organization_types_has_o{$index_mark}`
                      FOREIGN KEY (`organization_id`)
                      REFERENCES `" . $this->db->dbprefix . "organizations` (`organization_id`)
                      ON DELETE CASCADE
                      ON UPDATE NO ACTION)
                  ENGINE = InnoDB
                  DEFAULT CHARACTER SET = utf8
                  COLLATE = utf8_general_ci;");
                    
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . $this->db->dbprefix . "main_objects_organizations` (
                    `object_id` INT NOT NULL,
                    `organization_id` INT UNSIGNED NOT NULL,
                    `organization_type_id` INT UNSIGNED NOT NULL,
                    PRIMARY KEY (`object_id`, `organization_id`, `organization_type_id`),
                    INDEX `fk_main_object_has_organizations_organizations{$index_mark}_idx` (`organization_id` ASC),
                    INDEX `fk_main_object_has_organizations_main_object{$index_mark}_idx` (`object_id` ASC),
                    INDEX `fk_main_object_organizations_organization_types{$index_mark}_idx` (`organization_type_id` ASC),
                    CONSTRAINT `fk_main_object_has_organizations_main_object{$index_mark}`
                      FOREIGN KEY (`object_id`)
                      REFERENCES `" . $this->db->dbprefix . "main_object` (`id`)
                      ON DELETE CASCADE
                      ON UPDATE NO ACTION,
                    CONSTRAINT `fk_main_object_has_organizations_organizations{$index_mark}`
                      FOREIGN KEY (`organization_id`)
                      REFERENCES `" . $this->db->dbprefix . "organizations` (`organization_id`)
                      ON DELETE CASCADE
                      ON UPDATE NO ACTION,
                    CONSTRAINT `fk_main_object_organizations_organization_types{$index_mark}`
                      FOREIGN KEY (`organization_type_id`)
                      REFERENCES `" . $this->db->dbprefix . "organization_types` (`organization_type_id`)
                      ON DELETE CASCADE
                      ON UPDATE NO ACTION)
                  ENGINE = InnoDB
                  DEFAULT CHARACTER SET = utf8
                  COLLATE = utf8_general_ci;");
                    
        $this->db->query("ALTER TABLE `" . $this->db->dbprefix . "handbks` ADD COLUMN `adm_url` VARCHAR(155) NULL AFTER `description`;");
        $this->db->query("ALTER TABLE `" . $this->db->dbprefix . "organizations` ADD UNIQUE INDEX `alias_UNIQUE` (`alias` ASC);");
        
        $this->db->query("INSERT INTO `" . $this->db->dbprefix . "organization_types` (`name`, `params`) VALUES ('Застройщик', '{\"address\":{\"title\":\"Адрес\",\"type\":\"text\"},\"phone\":{\"title\":\"Телефон\",\"type\":\"text\"},\"site\":{\"title\":\"Сайт\",\"type\":\"url\"}}');");
        $this->db->query("INSERT INTO `" . $this->db->dbprefix . "organization_types` (`name`, `params`) VALUES ('Продавец', '{\"address\":{\"title\":\"Адрес\",\"type\":\"text\"},\"phone\":{\"title\":\"Телефон\",\"type\":\"text\"},\"site\":{\"title\":\"Сайт\",\"type\":\"url\"}}');");
        $this->db->query("INSERT INTO `" . $this->db->dbprefix . "file_categories` (`name`, `prefix`, `uri`, `uri_adm`, `parent_table`) VALUES ('Организации', 'organizations', '/organizations/{alias}', '/admin/organizations/edit/{id}', 'organizations');");
        
        $this->db->query("INSERT INTO `" . $this->db->dbprefix . "file_categories_proportions` (`file_category_id`, `proportion_id`) "
                . "VALUES ((SELECT file_category_id FROM " . $this->db->dbprefix . "file_categories WHERE prefix = 'organizations'), "
                . "(SELECT proportion_id FROM " . $this->db->dbprefix . "proportions WHERE name = '1140x730'));");
        $this->db->query("INSERT INTO `" . $this->db->dbprefix . "file_categories_proportions` (`file_category_id`, `proportion_id`) "
                . "VALUES ((SELECT file_category_id FROM " . $this->db->dbprefix . "file_categories WHERE prefix = 'organizations'), "
                . "(SELECT proportion_id FROM " . $this->db->dbprefix . "proportions WHERE name = '570x380'));");
        
        $this->db->query("INSERT INTO `" . $this->db->dbprefix . "handbks` (`name`, `table`, `adm_url`) VALUES ('Организации', 'organizations', '/admin/organizations/');");
        $this->db->query("UPDATE `" . $this->db->dbprefix . "handbks` SET `adm_url`='/admin/glossary' WHERE `table`='glossary';");
    }

    public function down() {
        
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0;");
        $this->db->query("DROP TABLE IF EXISTS `" . $this->db->dbprefix . "organizations_tags`;");
        $this->db->query("DROP TABLE IF EXISTS `" . $this->db->dbprefix . "organizations_organization_types`;");
        $this->db->query("DROP TABLE IF EXISTS `" . $this->db->dbprefix . "main_objects_organizations`;");
        $this->db->query("DROP TABLE IF EXISTS `" . $this->db->dbprefix . "organizations`;");
        $this->db->query("DROP TABLE IF EXISTS `" . $this->db->dbprefix . "organization_types`;");
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1;");
        
        $this->db->query("ALTER TABLE " . $this->db->dbprefix . "handbks DROP COLUMN adm_url;");
        $this->db->query("DELETE FROM `" . $this->db->dbprefix . "file_categories_proportions` "
                . "WHERE `file_category_id` IN (SELECT file_category_id FROM " . $this->db->dbprefix . "file_categories WHERE prefix = 'organizations');");
        $this->db->query("DELETE FROM `" . $this->db->dbprefix . "file_categories` WHERE `prefix`='organizations';");
        
    }

}
