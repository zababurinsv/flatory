<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * migration
 *
 * @date 13.06.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Migration_Links extends CI_Migration {

    private $db_name = '';

    public function __construct($config = array()) {
        $this->db_name = $this->db->database;
    }

    public function up() {

        $this->db->query("
            CREATE TABLE `" . $this->db->dbprefix . "links` (
            `link_id` INT NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(255) NOT NULL,
            `link` VARCHAR(500) NOT NULL,
            `object_id` INT NOT NULL,
            `status` TINYINT NOT NULL,
            PRIMARY KEY (`link_id`));
          ");
    }

    public function down() {
        $this->db->query("DROP TABLE IF EXISTS " . $this->db->dbprefix . "links");
    }

}
