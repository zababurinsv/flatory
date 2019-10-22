<?php

/**
 * 016_geo
 *
 * @date 25.06.2016
 */
class Migration_geo extends CI_Migration {

    public function __construct($config = array()) {
        parent::__construct($config);
    }

    public function up() {
        /**
         * 
        INSERT INTO `c5146_flatory_beta`.`cat_file_categories_proportions` (`file_category_id`, `proportion_id`) VALUES ('15', '1');
        INSERT INTO `c5146_flatory_beta`.`cat_file_categories_proportions` (`file_category_id`, `proportion_id`) VALUES ('15', '3');
        INSERT INTO `c5146_flatory_beta`.`cat_file_categories_proportions` (`file_category_id`, `proportion_id`) VALUES ('16', '1');
        INSERT INTO `c5146_flatory_beta`.`cat_file_categories_proportions` (`file_category_id`, `proportion_id`) VALUES ('16', '3');
        INSERT INTO `c5146_flatory_beta`.`cat_file_categories_proportions` (`file_category_id`, `proportion_id`) VALUES ('17', '1');
        INSERT INTO `c5146_flatory_beta`.`cat_file_categories_proportions` (`file_category_id`, `proportion_id`) VALUES ('17', '3');
        INSERT INTO `c5146_flatory_beta`.`cat_file_categories_proportions` (`file_category_id`, `proportion_id`) VALUES ('18', '1');
        INSERT INTO `c5146_flatory_beta`.`cat_file_categories_proportions` (`file_category_id`, `proportion_id`) VALUES ('18', '3');

         */
        $this->_up_object_id();
    }

    public function down() {
        $this->_down_object_id();
    }
    
    private function _geo_description() {
        
        $tables = ['geo_area', 'populated_locality', 'district', 'square'];
        
        foreach ($tables as $it)
            $this->db->query("ALTER TABLE `" . $this->db->dbprefix . $it . "` ADD COLUMN `description` TEXT AFTER `name`;");
        
    }
    
    public function _geo_description_down() {
        $tables = ['geo_area', 'populated_locality', 'district', 'square'];
        
        foreach ($tables as $it)
            $this->db->query("ALTER TABLE `" . $this->db->dbprefix . $it . "` DROP COLUMN `description`;");
    }
    
    private function _up_object_id(){
        $this->db->query("ALTER TABLE `" . $this->db->dbprefix . "posts` ADD COLUMN `object_id` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `file_category_id`;");
        $this->db->query("ALTER TABLE `" . $this->db->dbprefix . "posts` ADD INDEX `category_object` (`file_category_id` ASC, `object_id` ASC);");
    }
    
    private function _down_object_id(){
        $this->db->query("ALTER TABLE `" . $this->db->dbprefix . "posts` DROP COLUMN `object_id`;");
        @$this->db->query("DROP INDEX `category_object` ON `" . $this->db->dbprefix . "posts`;");
    }

}
