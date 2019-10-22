<?php

/**
 * 017_geo
 *
 * @date 25.06.2016
 */
class Migration_metro_map extends CI_Migration {

    public function __construct($config = array()) {
        parent::__construct($config);
    }

    public function up() {
        $this->db->query("ALTER TABLE `" . $this->db->dbprefix . "metro_station` ADD COLUMN `params` TEXT NULL AFTER `metro_line_id;");
        $this->_create_table_relation();
    }

    public function down() {
        $this->db->query("ALTER TABLE `" . $this->db->dbprefix . "metro_station` DROP COLUMN `params`;");
    }

    private function _create_table_relation() {
        $this->db->query("CREATE TABLE `cat_metro_station_metro_line` (
                    `metro_station_id` INT(11) UNSIGNED NOT NULL,
                    `metro_line_id` INT(11) UNSIGNED NOT NULL,
                    PRIMARY KEY (`metro_station_id`, `metro_line_id`),
                    INDEX `metro_line_id_idx` (`metro_line_id` ASC),
                    CONSTRAINT `metro_station_id`
                      FOREIGN KEY (`metro_station_id`)
                      REFERENCES `cat_metro_station` (`metro_station_id`)
                      ON DELETE CASCADE
                      ON UPDATE NO ACTION,
                    CONSTRAINT `metro_line_id`
                      FOREIGN KEY (`metro_line_id`)
                      REFERENCES `cat_metro_line` (`metro_line_id`)
                      ON DELETE CASCADE
                      ON UPDATE NO ACTION)
                  ENGINE = InnoDB
                  DEFAULT CHARACTER SET = utf8
                  COLLATE = utf8_general_ci;");
        
        // insert into cat_metro_station_metro_line (SELECT metro_station_id, metro_line_id FROM cat_metro_station);

    }

}
