<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Миграция Хранилища файлов
 *
 * @date 21.02.2015
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class Migration_Storage_Files extends CI_Migration {

    private $db_name = '';

    public function __construct($config = array()) {
        $this->db_name = $this->db->database;
    }

    public function up() {
//        var_dump('ffff');die;
//        $this->down(); return false;
        $this->file_types();
        $this->file_formats();
        $this->storage_files();
        $this->proportions();
        $this->files_proportions();
        $this->file_categories();
        $this->file_categories_proportions();
        $this->file_involves();
        $this->image_albums();
        $this->files_image_albums();
        $this->tags();
        $this->files_tags();
    }

    public function down() {
        
        $this->files_proportions_drop();
        $this->files_image_albums_drop();
        $this->files_tags_drop();
        
        $this->storage_files_drop();
        $this->file_formats_drop();
        $this->file_types_drop();
        
        $this->file_categories_proportions_drop();
        $this->file_involves_drop();
        
        $this->proportions_drop();
        $this->file_categories_drop();
        
        $this->image_albums_drop();
        $this->tags_drop();

    }

    /**
     * Создание и заполнение первичными данными file_types
     */
    private function file_types() {
        // структура `file_types` 
        $this->db->query(
                "CREATE TABLE IF NOT EXISTS " . $this->db->dbprefix . "file_types (
                `file_type_id` INT NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(155) NOT NULL,
                `path` VARCHAR(155) NOT NULL COMMENT 'путь от корня проекта',
                `description` TEXT NULL,
                `status` INT NOT NULL DEFAULT 1,
                PRIMARY KEY (`file_type_id`))
                ENGINE = InnoDB"
        );

        // Дамп данных таблицы `file_types`
//        $this->db->query(
//                "INSERT INTO " . $this->db->dbprefix . "file_types (`file_type_id`, `name`,`path`,`description`) VALUES
//                (1, 'Изображения', '/images/original/', ''),
//                (2, 'Документы', '/docs/', '');"
//        );
    }

    /**
     * Откат таблицы file_types
     */
    private function file_types_drop() {
        // Для быстрого удаления
        // Сначала очищаем таблицу
//        $this->db->query("TRUNCATE TABLE " . $this->db->dbprefix . "file_types;");
        // Потом удаляем
        $this->db->query("DROP TABLE IF EXISTS " . $this->db->dbprefix . "file_types;");
    }

    /**
     * Создание и заполнение первичными данными file_formats
     */
    private function file_formats() {
        // структура 
        $this->db->query(
                "CREATE TABLE IF NOT EXISTS `" . $this->db->dbprefix . "file_formats` (
                `file_format_id` INT NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(155) NOT NULL,
                `ext` VARCHAR(5) NOT NULL,
                `status` INT NOT NULL DEFAULT '1',
                `file_type_id` INT NOT NULL,
                PRIMARY KEY (`file_format_id`),
                INDEX `fk_file_formats_file_types1_idx` (`file_type_id` ASC),
                CONSTRAINT `fk_file_formats_file_types1`
                  FOREIGN KEY (`file_type_id`)
                  REFERENCES `" . $this->db->dbprefix . "file_types` (`file_type_id`)
                  ON DELETE NO ACTION
                  ON UPDATE NO ACTION)
                ENGINE = InnoDB;"
        );
//        $this->db->query(
//                "INSERT INTO " . $this->db->dbprefix . "file_formats (`file_format_id`, `name`,`ext`,`file_type_id`) VALUES
//                (1, 'jpg', 'jpg', '1'),
//                (2, 'png', 'png', '1'),
//                (3, 'pdf', 'pdf', '2'),
//                (4, 'xls', 'xls', '2'),
//                (5, 'doc', 'doc', '2');"
//        );
    }

    /**
     * Откат таблицы file_formats
     */
    private function file_formats_drop() {
        // Для быстрого удаления
        // Сначала очищаем таблицу
//        $this->db->query("TRUNCATE TABLE " . $this->db->dbprefix . "file_formats;");
        // Потом удаляем
        $this->db->query("DROP TABLE IF EXISTS " . $this->db->dbprefix . "file_formats;");
    }

    /**
     * storage_files
     */
    private function storage_files() {
        $this->db->query(
                "CREATE TABLE IF NOT EXISTS `" . $this->db->dbprefix . "storage_files` (
                `file_id` INT NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(32) NOT NULL,  -- md5
                `original_name` VARCHAR(255) NOT NULL,
                `user_id` INT NOT NULL,
                `file_format_id` INT NOT NULL,
                `size` INT NOT NULL DEFAULT 0 COMMENT 'для файлов без пропорций',
                `description` TEXT NULL,
                `autor` TEXT NULL,
                `updated` TIMESTAMP NOT NULL ,
                `created` TIMESTAMP NOT NULL,
                `status` INT NOT NULL DEFAULT 1,
                PRIMARY KEY (`file_id`),
                INDEX `fk_files_file_formats1_idx` (`file_format_id` ASC),
                CONSTRAINT `fk_files_file_formats1`
                  FOREIGN KEY (`file_format_id`)
                  REFERENCES `" . $this->db->dbprefix . "file_formats` (`file_format_id`)
                  ON DELETE NO ACTION
                  ON UPDATE NO ACTION)
                ENGINE = InnoDB;"
        );
    }

    /**
     * Откат таблицы storage_files
     */
    private function storage_files_drop() {
        // Для быстрого удаления
        // Сначала очищаем таблицу
//        $this->db->query("TRUNCATE TABLE " . $this->db->dbprefix . "storage_files;");
        // Потом удаляем
        $this->db->query("DROP TABLE IF EXISTS " . $this->db->dbprefix . "storage_files;");
    }

    /**
     * proportions
     */
    private function proportions() {
        $this->db->query(
                "CREATE TABLE IF NOT EXISTS `" . $this->db->dbprefix . "proportions` (
                `proportion_id` INT NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(45) NOT NULL,
                `x` INT NOT NULL,
                `y` INT NOT NULL,
                `status` INT NOT NULL DEFAULT 1,
                PRIMARY KEY (`proportion_id`))
                ENGINE = InnoDB;"
        );
    }

    /**
     * Откат таблицы proportions
     */
    private function proportions_drop() {
        // Для быстрого удаления
        // Сначала очищаем таблицу
//        $this->db->query("TRUNCATE TABLE " . $this->db->dbprefix . "proportions;");
        // Потом удаляем
        $this->db->query("DROP TABLE IF EXISTS " . $this->db->dbprefix . "proportions;");
    }

    /**
     * files_proportions
     */
    private function files_proportions() {
        $this->db->query(
                "CREATE TABLE IF NOT EXISTS `" . $this->db->dbprefix . "files_proportions` (
                `file_id` INT NOT NULL,
                `proportion_id` INT NOT NULL,
                `size` INT NOT NULL DEFAULT 0,
                `is_water_mark` TINYINT NOT NULL DEFAULT 0,
                PRIMARY KEY (`file_id`, `proportion_id`),
                INDEX `fk_files_has_proportions_proportions1_idx` (`proportion_id` ASC),
                INDEX `fk_files_has_proportions_files1_idx` (`file_id` ASC),
                CONSTRAINT `fk_files_has_proportions_files1`
                  FOREIGN KEY (`file_id`)
                  REFERENCES `" . $this->db->dbprefix . "storage_files` (`file_id`)
                  ON DELETE NO ACTION
                  ON UPDATE NO ACTION,
                CONSTRAINT `fk_files_has_proportions_proportions1`
                  FOREIGN KEY (`proportion_id`)
                  REFERENCES `" . $this->db->dbprefix . "proportions` (`proportion_id`)
                  ON DELETE NO ACTION
                  ON UPDATE NO ACTION)
                ENGINE = InnoDB;"
        );
    }

    /**
     * Откат таблицы files_proportions
     */
    private function files_proportions_drop() {
        // Для быстрого удаления
        // Сначала очищаем таблицу
//        $this->db->query("TRUNCATE TABLE " . $this->db->dbprefix . "files_proportions;");
        // Потом удаляем
        $this->db->query("DROP TABLE IF EXISTS " . $this->db->dbprefix . "files_proportions;");
    }

    /**
     * file_categories
     */
    private function file_categories() {
        $this->db->query(
                "CREATE TABLE IF NOT EXISTS `" . $this->db->dbprefix . "file_categories` (
                `file_category_id` INT NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(155) NOT NULL,
                `prefix` VARCHAR(45) NOT NULL COMMENT 'префикс для названий файлов',
                `uri` VARCHAR(255) NOT NULL,
                `uri_adm` VARCHAR(255) NOT NULL,
                `settings` TEXT NULL,
                `status` INT NOT NULL DEFAULT 1,
                PRIMARY KEY (`file_category_id`))
                ENGINE = InnoDB;"
        );
    }

    /**
     * Откат таблицы file_categories
     */
    private function file_categories_drop() {
        // Для быстрого удаления
        // Сначала очищаем таблицу
//        $this->db->query("TRUNCATE TABLE " . $this->db->dbprefix . "file_categories;");
        // Потом удаляем
        $this->db->query("DROP TABLE IF EXISTS " . $this->db->dbprefix . "file_categories;");
    }

    /**
     * file_categories_proportions
     */
    private function file_categories_proportions() {
        $this->db->query(
                "CREATE TABLE IF NOT EXISTS `" . $this->db->dbprefix . "file_categories_proportions` (
                `file_category_id` INT NOT NULL,
                `proportion_id` INT NOT NULL,
                PRIMARY KEY (`file_category_id`, `proportion_id`),
                INDEX `fk_file_categories_has_proportions_proportions1_idx` (`proportion_id` ASC),
                INDEX `fk_file_categories_has_proportions_file_categories1_idx` (`file_category_id` ASC),
                CONSTRAINT `fk_file_categories_has_proportions_file_categories1`
                  FOREIGN KEY (`file_category_id`)
                  REFERENCES `" . $this->db->dbprefix . "file_categories` (`file_category_id`)
                  ON DELETE NO ACTION
                  ON UPDATE NO ACTION,
                CONSTRAINT `fk_file_categories_has_proportions_proportions1`
                  FOREIGN KEY (`proportion_id`)
                  REFERENCES `" . $this->db->dbprefix . "proportions` (`proportion_id`)
                  ON DELETE NO ACTION
                  ON UPDATE NO ACTION)
                ENGINE = InnoDB;"
        );
    }

    /**
     * Откат таблицы file_categories_proportions
     */
    private function file_categories_proportions_drop() {
        // Для быстрого удаления
        // Сначала очищаем таблицу
//        $this->db->query("TRUNCATE TABLE " . $this->db->dbprefix . "file_categories_proportions;");
        // Потом удаляем
        $this->db->query("DROP TABLE IF EXISTS " . $this->db->dbprefix . "file_categories_proportions;");
    }

    /**
     * file_involves
     */
    private function file_involves() {

        $this->db->query(
                "CREATE TABLE IF NOT EXISTS `" . $this->db->dbprefix . "file_involves` (
                `file_involve_id` INT NOT NULL,
                `file_category_id` INT NOT NULL,
                `file_id` INT NOT NULL,
                `parent_id` INT NOT NULL COMMENT 'ид объекта включающего данный файл (ид новости, карточки)',
                `created` TIMESTAMP NOT NULL DEFAULT now(),
                PRIMARY KEY (`file_involve_id`),
                INDEX `fk_file_involves_file_categories1_idx` (`file_category_id` ASC),
                INDEX `fk_file_involves_files1_idx` (`file_id` ASC),
                CONSTRAINT `fk_file_involves_file_categories1`
                  FOREIGN KEY (`file_category_id`)
                  REFERENCES `" . $this->db->dbprefix . "file_categories` (`file_category_id`)
                  ON DELETE NO ACTION
                  ON UPDATE NO ACTION,
                CONSTRAINT `fk_file_involves_files1`
                  FOREIGN KEY (`file_id`)
                  REFERENCES `" . $this->db->dbprefix . "storage_files` (`file_id`)
                  ON DELETE NO ACTION
                  ON UPDATE NO ACTION)
                ENGINE = InnoDB"
        );
    }

    /**
     * Откат таблицы file_categories_proportions
     */
    private function file_involves_drop() {
        // Для быстрого удаления
        // Сначала очищаем таблицу
//        $this->db->query("TRUNCATE TABLE " . $this->db->dbprefix . "file_involves;");
        // Потом удаляем
        $this->db->query("DROP TABLE IF EXISTS " . $this->db->dbprefix . "file_involves;");
    }

    /**
     * image_albums
     */
    private function image_albums() {
        $this->db->query(
                "CREATE TABLE IF NOT EXISTS `" . $this->db->dbprefix . "image_albums` (
                `image_album_id` INT NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(155) NOT NULL,
                `description` TEXT NULL,
                `created` TIMESTAMP NOT NULL,
                `updated` TIMESTAMP NOT NULL ,
                `status` INT NOT NULL DEFAULT 1,
                PRIMARY KEY (`image_album_id`))
                ENGINE = InnoDB"
        );
    }

    /**
     * Откат таблицы file_categories_proportions
     */
    private function image_albums_drop() {
        // Для быстрого удаления
        // Сначала очищаем таблицу
//        $this->db->query("TRUNCATE TABLE " . $this->db->dbprefix . "image_albums;");
        // Потом удаляем
        $this->db->query("DROP TABLE IF EXISTS " . $this->db->dbprefix . "image_albums;");
    }

    /**
     * files_image_albums
     */
    private function files_image_albums() {
        $this->db->query(
                "CREATE TABLE IF NOT EXISTS `" . $this->db->dbprefix . "files_image_albums` (
                `file_id` INT NOT NULL,
                `image_album_id` INT NOT NULL,
                PRIMARY KEY (`file_id`, `image_album_id`),
                INDEX `fk_albums_has_files_files1_idx` (`file_id` ASC),
                INDEX `fk_albums_has_files_albums1_idx` (`image_album_id` ASC),
                CONSTRAINT `fk_albums_has_files_albums1`
                  FOREIGN KEY (`image_album_id`)
                  REFERENCES `" . $this->db->dbprefix . "image_albums` (`image_album_id`)
                  ON DELETE NO ACTION
                  ON UPDATE NO ACTION,
                CONSTRAINT `fk_albums_has_files_files1`
                  FOREIGN KEY (`file_id`)
                  REFERENCES `" . $this->db->dbprefix . "storage_files` (`file_id`)
                  ON DELETE NO ACTION
                  ON UPDATE NO ACTION)
                ENGINE = InnoDB;"
        );
    }

    /**
     * Откат таблицы file_categories_proportions
     */
    private function files_image_albums_drop() {
        // Для быстрого удаления
        // Сначала очищаем таблицу
//        $this->db->query("TRUNCATE TABLE " . $this->db->dbprefix . "files_image_albums;");
        // Потом удаляем
        $this->db->query("DROP TABLE IF EXISTS " . $this->db->dbprefix . "files_image_albums;");
    }

    /**
     * tags
     */
    private function tags() {
        $this->db->query(
                "CREATE TABLE IF NOT EXISTS `" . $this->db->dbprefix . "tags` (
                `tag_id` INT NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(155) NOT NULL,
                `description` TEXT NULL,
                PRIMARY KEY (`tag_id`))
                ENGINE = InnoDB;"
        );
    }

    /**
     * Откат таблицы file_categories_proportions
     */
    private function tags_drop() {
        // Для быстрого удаления
        // Сначала очищаем таблицу
//        $this->db->query("TRUNCATE TABLE " . $this->db->dbprefix . "tags;");
        // Потом удаляем
        $this->db->query("DROP TABLE IF EXISTS " . $this->db->dbprefix . "tags;");
    }

    /**
     * files_tags
     */
    private function files_tags() {
        $this->db->query(
                "CREATE TABLE IF NOT EXISTS `" . $this->db->dbprefix . "files_tags` (
                `file_id` INT NOT NULL,
                `tag_id` INT NOT NULL,
                PRIMARY KEY (`file_id`, `tag_id`),
                INDEX `fk_files_has_tags_tags1_idx` (`tag_id` ASC),
                INDEX `fk_files_has_tags_files1_idx` (`file_id` ASC),
                CONSTRAINT `fk_files_has_tags_files1`
                  FOREIGN KEY (`file_id`)
                  REFERENCES `" . $this->db->dbprefix . "storage_files` (`file_id`)
                  ON DELETE NO ACTION
                  ON UPDATE NO ACTION,
                CONSTRAINT `fk_files_has_tags_tags1`
                  FOREIGN KEY (`tag_id`)
                  REFERENCES `" . $this->db->dbprefix . "tags` (`tag_id`)
                  ON DELETE NO ACTION
                  ON UPDATE NO ACTION)
                ENGINE = InnoDB;"
        );
    }
    
    /**
     * Откат таблицы file_categories_proportions
     */
    private function files_tags_drop() {
        // Для быстрого удаления
        // Сначала очищаем таблицу
//        $this->db->query("TRUNCATE TABLE " . $this->db->dbprefix . "files_tags;");
        // Потом удаляем
        $this->db->query("DROP TABLE IF EXISTS " . $this->db->dbprefix . "files_tags;");
    }

}
