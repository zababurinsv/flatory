<?php

/**
 * 017_geo
 *
 * @date 25.06.2016
 */
class Migration_count_objects extends CI_Migration {

    public function __construct($config = array()) {
        parent::__construct($config);
    }

    public function up() {
        $this->_geo_count_objects();
        $this->_handbks_count_objects();
        $this->alter_index();
    }

    public function down() {
        $this->_geo_count_objects_down();
        $this->_handbks_count_objects_down();
    }

    private function _geo_count_objects() {

        // ALTER TABLE `cat_square` ADD COLUMN `count_objects` INT NOT NULL DEFAULT 0 AFTER `name`;
        // ALTER TABLE `cat_district` ADD INDEX `count_objects` (`count_objects` ASC);
        $tables = ['geo_area', 'populated_locality', 'district', 'square', 'metro_station', 'direction'];

        foreach ($tables as $it){
            $this->db->query("ALTER TABLE `" . $this->db->dbprefix . $it . "` ADD COLUMN `count_objects` INT NOT NULL DEFAULT 0 AFTER `name`;"); 
            $this->db->query("ALTER TABLE `" . $this->db->dbprefix . $it . "` ADD INDEX `count_objects` (`count_objects` ASC);"); 
        }
           
    }

    public function _geo_count_objects_down() {
        $tables = ['geo_area', 'populated_locality', 'district', 'square', 'metro_station', 'direction'];

        foreach ($tables as $it)
            $this->db->query("ALTER TABLE `" . $this->db->dbprefix . $it . "` DROP COLUMN `count_objects`;");
    }

    private function _handbks_count_objects() {
        $this->_handbks_count_objects_down();
        $this->db->query("
            DELIMITER $$
            CREATE PROCEDURE `handbks_object_counts_update` ()
            BEGIN
	
                -- square
                UPDATE cat_square as s
                SET count_objects = ( 
                        select count(square_id)
                        from cat_meta as m
                        left join cat_main_object as o on m.id_object = o.id
                        WHERE m.square_id = s.square_id and o.status = 1
                );
                -- geo_area
                UPDATE cat_geo_area as s
                SET count_objects = ( 
                        select count(geo_area_id)
                        from cat_meta as m
                        left join cat_main_object as o on m.id_object = o.id
                        WHERE m.geo_area_id = s.geo_area_id and o.status = 1
                );
                -- populated_locality
                UPDATE cat_populated_locality as s
                SET count_objects = ( 
                        select count(populated_locality_id)
                        from cat_meta as m
                        left join cat_main_object as o on m.id_object = o.id
                        WHERE m.populated_locality_id = s.populated_locality_id and o.status = 1
                );
                -- district
                UPDATE cat_district as s
                SET count_objects = ( 
                        select count(district_id)
                        from cat_meta as m
                        left join cat_main_object as o on m.id_object = o.id
                        WHERE m.district_id = s.district_id and o.status = 1
                );
                -- metro_station
                UPDATE cat_metro_station as s
                SET count_objects = ( 
                        SELECT count(object_id) FROM cat_meta_metro  as mm
                        left join cat_main_object as o on mm.object_id = o.id
                        where metro_id = s.metro_station_id and o.status = 1
                );
                -- direction
                UPDATE cat_direction as s
                SET count_objects = ( 
                        select count(geo_direction_id)
                        from cat_meta as m
                        left join cat_main_object as o on m.id_object = o.id
                        WHERE m.geo_direction_id = s.id and o.status = 1
                );
            END$$
            DELIMITER ;
            ");
    }

    private function _handbks_count_objects_down() {
        $this->db->query("DROP PROCEDURE if exists handbks_object_counts_update;");
    }

    public function alter_index() {
        $this->db->query("ALTER TABLE `" . $this->db->dbprefix . "meta_metro` 
                CHANGE COLUMN `object_id` `object_id` INT(11) NOT NULL ,
                CHANGE COLUMN `metro_id` `metro_id` INT(11) NOT NULL ,
                CHANGE COLUMN `status` `status` TINYINT(2) NOT NULL DEFAULT 0 ,
                ADD INDEX `object_id` (`object_id` ASC),
                ADD INDEX `metro_id` (`metro_id` ASC);");
        
        $this->db->query("ALTER TABLE `" . $this->db->dbprefix . "delivery` 
                ADD INDEX `object_id` (`object_id` ASC),
                ADD INDEX `year` (`year` ASC),
                ADD INDEX `object_year` (`object_id` ASC, `year` ASC);");
        
        $this->db->query("ALTER TABLE `" . $this->db->dbprefix . "flats` 
                ADD INDEX `object_id` (`object_id` ASC),
                ADD INDEX `room_id_object_id` (`room_id` ASC, `object_id` ASC);");
        
        $this->db->query("ALTER TABLE `" . $this->db->dbprefix . "meta` 
                ADD INDEX `object_id` (`id_object` ASC),
                ADD INDEX `zone_id` (`zone_id` ASC),
                ADD INDEX `geo_area_id` (`geo_area_id` ASC),
                ADD INDEX `populated_locality_id` (`populated_locality_id` ASC),
                ADD INDEX `district_id` (`district_id` ASC),
                ADD INDEX `square_id` (`square_id` ASC),
                ADD INDEX `geo_direction_id` (`geo_direction_id` ASC);");
        
        $this->db->query("ALTER TABLE `" . $this->db->dbprefix . "file_involves` 
                ADD INDEX `file_id_file_category_id` (`file_category_id` ASC, `file_id` ASC);");
        
        $this->db->query("CREATE TABLE `" . $this->db->dbprefix . "macro_regions` (
                `macro_region_id` INT NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NOT NULL,
                `status` TINYINT(1) NOT NULL DEFAULT 1,
                PRIMARY KEY (`macro_region_id`))
              ENGINE = InnoDB
              DEFAULT CHARACTER SET = utf8
              COLLATE = utf8_general_ci;");
    }
}
