<?php

//if (PHP_SAPI !== 'cli')
//    exit('No web access allowed');

/**
 * Settings for storage
 *
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Storage_settings extends CI_Controller {

    /**
     * Model
     * @var \Proportions 
     */
    public $Proportions;

    /**
     * Model
     * @param \File_Categories
     */
    public $File_Categories;

    public function __construct() {

        parent::__construct();
        $this->load->model('Proportions');
        $this->load->model('File_Categories');
    }

    public function up() {


        $this->_set_foreign_keys();
        
        $this->query("SET foreign_key_checks = 0;");

        $this->db->query("TRUNCATE TABLE " . $this->db->dbprefix . "file_categories_proportions;");
        $this->db->query("TRUNCATE TABLE `" . $this->db->dbprefix . "files_proportions`;");
        $this->db->query("TRUNCATE TABLE `" . $this->db->dbprefix . "proportions`;");
        $this->db->query("TRUNCATE TABLE `" . $this->db->dbprefix . "file_categories`;");
        $this->db->query("TRUNCATE TABLE `" . $this->db->dbprefix . "file_involves`;");
        $this->db->query("TRUNCATE TABLE `" . $this->db->dbprefix . "files_image_albums`;");
        $this->db->query("TRUNCATE TABLE `" . $this->db->dbprefix . "image_albums`;");
        $this->db->query("TRUNCATE TABLE `" . $this->db->dbprefix . "storage_files`;");
        $proportions = array(
            array('proportion_id' => 1, 'name' => '1140x730', 'x' => '1140', 'y' => '730',),
            array('proportion_id' => 2, 'name' => '255x170', 'x' => '255', 'y' => '170',),
            array('proportion_id' => 3, 'name' => '570x380', 'x' => '570', 'y' => '380',),
        );
        // create proportions
        foreach ($proportions as $proportion) {
            self::deleteDir(DOCROOT . 'images' . DIRECTORY_SEPARATOR . $proportion['name']);
            $this->Proportions->create($proportion);
        }

        // categories
        $this->db->query("INSERT INTO `" . $this->db->dbprefix . "file_categories` VALUES "
                . "(2,'Каталог','catalog','/catalog/{alias}','/admin/objects/general_info/{id}',NULL,'main_object',1),"
                . "(3,'Планировки','plans','/catalog/{alias}/#plans','/admin/objects/plan/{id}',NULL,'main_object',1),"
                . "(4,'Фото строительства','photo_construction','/catalog/{alias}/#photo_construction','/admin/objects/gallery/{id}',NULL,'main_object',1),"
                . "(5,'Новости','news','/news/{alias}','/admin/news/edit/{id}',NULL,'news',1),"
                . "(6,'Строй-блог','article','/articles/{alias}','/admin/article/edit/{id}',NULL,'article',1),"
                . "(7,'Инфраструктура','infrastructure','/catalog/{alias}/#infrastructure','/admin/objects/infrastructure/{id}',NULL,'main_object',1),"
                . "(8,'Застройщики','builders','/','/admin/users/edit_builders/{id}',NULL,'',1),"
                . "(9,'Продавцы','sellers','/','/admin/users/edit_sellers/{id}',NULL,'',1),"
                . "(10,'Карточка объекта','cart','/catalog/','/admin/cart/{id}',NULL,'',1),"
                . "(11,'Документы','docs','/catalog/{alias}/#docs','/admin/objects/documents/{id}',NULL,'',1)"
                . ";");

        // set categories_proportions
        $this->Proportions->set_category_proportions(2, 1);
        $this->Proportions->set_category_proportions(2, 3);
        $this->Proportions->set_category_proportions(3, 1);
        $this->Proportions->set_category_proportions(3, 2);
        $this->Proportions->set_category_proportions(3, 3);
        $this->Proportions->set_category_proportions(4, 1);
        $this->Proportions->set_category_proportions(4, 2);
        $this->Proportions->set_category_proportions(4, 3);
        $this->Proportions->set_category_proportions(5, 1);
        $this->Proportions->set_category_proportions(5, 3);
        $this->Proportions->set_category_proportions(6, 1);
        $this->Proportions->set_category_proportions(6, 3);
        $this->Proportions->set_category_proportions(7, 1);
        $this->Proportions->set_category_proportions(7, 3);
    }

    /**
     * set cascade delete
     */
    private function _set_foreign_keys() {
        // set cascade delete
//        $this->query("ALTER TABLE `" . $this->db->dbprefix . "files_proportions` 
//                DROP FOREIGN KEY `fk_files_has_proportions_files1`,
//                DROP FOREIGN KEY `fk_files_has_proportions_proportions1`;");
//        $this->query("ALTER TABLE `" . $this->db->dbprefix . "files_proportions` 
//                ADD CONSTRAINT `fk_files_has_proportions_files2`
//                  FOREIGN KEY (`file_id`)
//                  REFERENCES `" . $this->db->dbprefix . "storage_files` (`file_id`)
//                  ON DELETE CASCADE
//                  ON UPDATE NO ACTION,
//                ADD CONSTRAINT `fk_files_has_proportions_proportions2`
//                  FOREIGN KEY (`proportion_id`)
//                  REFERENCES `" . $this->db->dbprefix . "proportions` (`proportion_id`)
//                  ON DELETE CASCADE
//                  ON UPDATE NO ACTION;");

//        $this->query("ALTER TABLE `" . $this->db->dbprefix . "file_categories_proportions` 
//                DROP FOREIGN KEY `fk_file_categories_has_proportions_file_categories1`,
//                DROP FOREIGN KEY `fk_file_categories_has_proportions_proportions1`;");
//        $this->query("ALTER TABLE `" . $this->db->dbprefix . "file_categories_proportions` 
//                ADD CONSTRAINT `fk_file_categories_has_proportions_file_categories3`
//                  FOREIGN KEY (`file_category_id`)
//                  REFERENCES `" . $this->db->dbprefix . "file_categories` (`file_category_id`)
//                  ON DELETE NO ACTION
//                  ON UPDATE CASCADE,
//                ADD CONSTRAINT `fk_file_categories_has_proportions_proportions3`
//                  FOREIGN KEY (`proportion_id`)
//                  REFERENCES `" . $this->db->dbprefix . "proportions` (`proportion_id`)
//                  ON DELETE NO ACTION
//                  ON UPDATE CASCADE;");

//        $this->query("ALTER TABLE `" . $this->db->dbprefix . "file_involves` 
//                DROP FOREIGN KEY `fk_file_involves_files1`,
//                DROP FOREIGN KEY `fk_file_involves_file_categories1`;");
//        $this->query("ALTER TABLE `" . $this->db->dbprefix . "file_involves` 
//                ADD CONSTRAINT `fk_file_involves_files2`
//                  FOREIGN KEY (`file_id`)
//                  REFERENCES `" . $this->db->dbprefix . "storage_files` (`file_id`)
//                  ON DELETE CASCADE
//                  ON UPDATE NO ACTION,
//                ADD CONSTRAINT `fk_file_involves_file_categories2`
//                  FOREIGN KEY (`file_category_id`)
//                  REFERENCES `" . $this->db->dbprefix . "file_categories` (`file_category_id`)
//                  ON DELETE CASCADE
//                  ON UPDATE NO ACTION;");

//        $this->query("ALTER TABLE `" . $this->db->dbprefix . "image_albums`  ADD INDEX `fk_cat_image_albums_1_idx` (`file_category_id` ASC);");
////        $this->query("ALTER TABLE `" . $this->db->dbprefix . "image_albums`  DROP FOREIGN KEY `fk_cat_image_albums_1`;");
//        $this->query("ALTER TABLE `" . $this->db->dbprefix . "image_albums` 
//                ADD CONSTRAINT `fk_cat_image_albums_2`
//                  FOREIGN KEY (`file_category_id`)
//                  REFERENCES `" . $this->db->dbprefix . "file_categories` (`file_category_id`)
//                  ON DELETE CASCADE
//                  ON UPDATE NO ACTION;");

//        $this->query("ALTER TABLE `" . $this->db->dbprefix . "files_image_albums` 
//                DROP FOREIGN KEY `fk_albums_has_files_albums1`,
//                DROP FOREIGN KEY `fk_albums_has_storage_files1`;");
//        $this->query("ALTER TABLE `" . $this->db->dbprefix . "files_image_albums` 
//                ADD CONSTRAINT `fk_albums_has_files_albums2`
//                  FOREIGN KEY (`image_album_id`)
//                  REFERENCES `" . $this->db->dbprefix . "image_albums` (`image_album_id`)
//                  ON DELETE CASCADE
//                  ON UPDATE NO ACTION,
//                ADD CONSTRAINT `fk_albums_has_storage_files2`
//                  FOREIGN KEY (`file_id`)
//                  REFERENCES `" . $this->db->dbprefix . "storage_files` (`file_id`)
//                  ON DELETE CASCADE
//                  ON UPDATE NO ACTION;");
    }

    private function query($query) {
        $this->db->query($query);
    }

    public static function deleteDir($dirPath) {
        if (!is_dir($dirPath)) {
//            throw new InvalidArgumentException("$dirPath must be a directory");
            return;
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

}
