<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * migration
 *
 * @date 10.05.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Migration_St_Files_category extends CI_Migration {
    
    private $db_name = '';

    public function __construct($config = array()) {
        $this->db_name = $this->db->database;
    }
    
    public function up() {
        $this->db->query("ALTER TABLE `". $this->db->dbprefix ."file_categories` ADD COLUMN `parent_table` VARCHAR(155) NOT NULL AFTER `settings`;");
        $this->db->query("SET FOREIGN_KEY_CHECKS=0;");
        $this->db->query("TRUNCATE TABLE `cat_file_categories`;");
        $this->db->query("INSERT INTO `cat_file_categories` (`file_category_id`,`name`,`prefix`,`uri`,`uri_adm`,`settings`,`parent_table`,`status`) VALUES (2,'Каталог','catalog','/catalog/{alias}','/admin/objects/general_info/{id}',NULL,'main_object',1);");
        $this->db->query("INSERT INTO `cat_file_categories` (`file_category_id`,`name`,`prefix`,`uri`,`uri_adm`,`settings`,`parent_table`,`status`) VALUES (3,'Планировки','plans','/catalog/{alias}/#plans','/admin/objects/plan/{id}',NULL,'main_object',1);");
        $this->db->query("INSERT INTO `cat_file_categories` (`file_category_id`,`name`,`prefix`,`uri`,`uri_adm`,`settings`,`parent_table`,`status`) VALUES (4,'Фото строительства','photo_construction','/catalog/{alias}/#photo_construction','/admin/objects/gallery/{id}',NULL,'main_object',1);");
        $this->db->query("INSERT INTO `cat_file_categories` (`file_category_id`,`name`,`prefix`,`uri`,`uri_adm`,`settings`,`parent_table`,`status`) VALUES (5,'Новости','news','/news/{alias}','/admin/news/edit/{id}',NULL,'news',1);");
        $this->db->query("INSERT INTO `cat_file_categories` (`file_category_id`,`name`,`prefix`,`uri`,`uri_adm`,`settings`,`parent_table`,`status`) VALUES (6,'Строй-блог','article','/articles/{alias}','/admin/article/edit/{id}',NULL,'article',1);");
        $this->db->query("INSERT INTO `cat_file_categories` (`file_category_id`,`name`,`prefix`,`uri`,`uri_adm`,`settings`,`parent_table`,`status`) VALUES (7,'Инфраструктура','infrastructure','/catalog/{alias}/#infrastructure','/admin/objects/infrastructure/{id}',NULL,'main_object',1);");
        $this->db->query("SET FOREIGN_KEY_CHECKS=1;");
    }
    
    public function down() {
        $this->db->query("ALTER TABLE `". $this->db->dbprefix ."file_categories` DROP COLUMN `parent_table`;");
    }
}
