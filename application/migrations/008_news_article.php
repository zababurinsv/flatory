<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * migration
 *
 * @date 24.05.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Migration_News_article extends CI_Migration {
    
    private $db_name = '';

    public function __construct($config = array()) {
        $this->db_name = $this->db->database;
    }
    
    public function up() {
        
        $this->db->query("ALTER TABLE `". $this->db->dbprefix ."news` 
            CHANGE COLUMN `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE COLUMN `name` `name` VARCHAR(100) NOT NULL ,
            CHANGE COLUMN `alias` `alias` VARCHAR(100) NOT NULL ,
            CHANGE COLUMN `content` `content` TEXT NOT NULL ,
            CHANGE COLUMN `title` `title` TEXT NOT NULL ,
            ADD COLUMN `file_id` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `alt`;");
        
        $this->db->query("ALTER TABLE `". $this->db->dbprefix ."article` 
            CHANGE COLUMN `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE COLUMN `name` `name` VARCHAR(100) NOT NULL ,
            CHANGE COLUMN `alias` `alias` VARCHAR(100) NOT NULL ,
            CHANGE COLUMN `content` `content` TEXT NOT NULL ,
            CHANGE COLUMN `title` `title` TEXT NOT NULL ,
            ADD COLUMN `file_id` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `alt`;
            ");
    }
    
    public function down() {
        $this->db->query("ALTER TABLE " . $this->db->dbprefix . "news DROP COLUMN file_id;");
        $this->db->query("ALTER TABLE " . $this->db->dbprefix . "article DROP COLUMN file_id;");
    }
}
