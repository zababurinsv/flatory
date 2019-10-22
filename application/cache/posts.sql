-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema flatory
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema flatory
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `flatory` DEFAULT CHARACTER SET utf8 ;
USE `flatory` ;

-- -----------------------------------------------------
-- Table `flatory`.`file_types`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `flatory`.`file_types` (
  `file_type_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(155) NOT NULL,
  `path` VARCHAR(155) NOT NULL COMMENT 'путь от корня проекта',
  `description` TEXT NULL,
  `status` INT NOT NULL DEFAULT 1,
  PRIMARY KEY (`file_type_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `flatory`.`file_formats`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `flatory`.`file_formats` (
  `file_format_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(155) NOT NULL,
  `ext` VARCHAR(5) NOT NULL,
  `status` INT NOT NULL DEFAULT '1',
  `file_type_id` INT NOT NULL,
  PRIMARY KEY (`file_format_id`),
  INDEX `fk_file_formats_file_types1_idx` (`file_type_id` ASC),
  CONSTRAINT `fk_file_formats_file_types1`
    FOREIGN KEY (`file_type_id`)
    REFERENCES `flatory`.`file_types` (`file_type_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `flatory`.`storage_files`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `flatory`.`storage_files` (
  `file_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `file_format_id` INT NOT NULL,
  `size` INT NOT NULL DEFAULT 0 COMMENT 'для файлов без пропорций',
  `description` TEXT NULL,
  `autor` TEXT NULL,
  `x` INT NOT NULL DEFAULT 0,
  `y` INT NOT NULL DEFAULT 0,
  `updated` DATETIME NOT NULL DEFAULT now(),
  `created` DATETIME NOT NULL,
  `status` INT NOT NULL DEFAULT 1,
  PRIMARY KEY (`file_id`),
  INDEX `fk_files_file_formats1_idx` (`file_format_id` ASC),
  CONSTRAINT `fk_files_file_formats1`
    FOREIGN KEY (`file_format_id`)
    REFERENCES `flatory`.`file_formats` (`file_format_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `flatory`.`proportions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `flatory`.`proportions` (
  `proportion_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `x` INT NOT NULL,
  `y` INT NOT NULL,
  `status` INT NOT NULL DEFAULT 1,
  PRIMARY KEY (`proportion_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `flatory`.`files_proportions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `flatory`.`files_proportions` (
  `file_id` INT NOT NULL,
  `proportion_id` INT NOT NULL,
  `size` INT NOT NULL DEFAULT 0,
  `is_water_mark` TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`file_id`, `proportion_id`),
  INDEX `fk_files_has_proportions_proportions1_idx` (`proportion_id` ASC),
  INDEX `fk_files_has_proportions_files1_idx` (`file_id` ASC),
  CONSTRAINT `fk_files_has_proportions_files1`
    FOREIGN KEY (`file_id`)
    REFERENCES `flatory`.`storage_files` (`file_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_files_has_proportions_proportions1`
    FOREIGN KEY (`proportion_id`)
    REFERENCES `flatory`.`proportions` (`proportion_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `flatory`.`file_categories`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `flatory`.`file_categories` (
  `file_category_id` INT NOT NULL,
  `name` VARCHAR(155) NOT NULL,
  `prefix` VARCHAR(45) NOT NULL COMMENT 'префикс для названий файлов',
  `uri` VARCHAR(255) NOT NULL,
  `uri_adm` VARCHAR(255) NOT NULL,
  `settings` TEXT NOT NULL DEFAULT '{}',
  `status` INT NOT NULL DEFAULT 1,
  `parent_table` VARCHAR(155) NOT NULL,
  PRIMARY KEY (`file_category_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `flatory`.`file_involves`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `flatory`.`file_involves` (
  `file_involve_id` INT NOT NULL,
  `file_category_id` INT NOT NULL,
  `file_id` INT NOT NULL,
  `parent_id` INT NOT NULL COMMENT 'ид объекта включающего данный файл (ид новости, карточки)',
  `parent_alias` VARCHAR(255) NULL,
  `sort` TINYINT NOT NULL DEFAULT 99,
  `created` DATETIME NOT NULL DEFAULT now(),
  PRIMARY KEY (`file_involve_id`),
  INDEX `fk_file_involves_file_categories1_idx` (`file_category_id` ASC),
  INDEX `fk_file_involves_files1_idx` (`file_id` ASC),
  CONSTRAINT `fk_file_involves_file_categories1`
    FOREIGN KEY (`file_category_id`)
    REFERENCES `flatory`.`file_categories` (`file_category_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_file_involves_files1`
    FOREIGN KEY (`file_id`)
    REFERENCES `flatory`.`storage_files` (`file_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `flatory`.`image_albums`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `flatory`.`image_albums` (
  `image_album_id` INT NOT NULL AUTO_INCREMENT,
  `file_category_id` INT NOT NULL,
  `object_id` INT NOT NULL DEFAULT 0,
  `name` VARCHAR(155) NOT NULL,
  `description` TEXT NULL,
  `created` DATETIME NOT NULL,
  `updated` DATETIME NOT NULL DEFAULT now(),
  `sort` INT NOT NULL DEFAULT 99,
  PRIMARY KEY (`image_album_id`, `file_category_id`),
  INDEX `fk_image_albums_file_categories1_idx` (`file_category_id` ASC),
  CONSTRAINT `fk_image_albums_file_categories1`
    FOREIGN KEY (`file_category_id`)
    REFERENCES `flatory`.`file_categories` (`file_category_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `flatory`.`files_image_albums`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `flatory`.`files_image_albums` (
  `file_id` INT NOT NULL,
  `image_album_id` INT NOT NULL,
  `file_involve_id` INT NOT NULL,
  `sort` INT NOT NULL DEFAULT 99,
  PRIMARY KEY (`file_id`, `image_album_id`),
  INDEX `fk_albums_has_files_files1_idx` (`file_id` ASC),
  INDEX `fk_albums_has_files_albums1_idx` (`image_album_id` ASC),
  INDEX `fk_files_image_albums_file_involves1_idx` (`file_involve_id` ASC),
  CONSTRAINT `fk_albums_has_files_albums1`
    FOREIGN KEY (`image_album_id`)
    REFERENCES `flatory`.`image_albums` (`image_album_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_albums_has_files_files1`
    FOREIGN KEY (`file_id`)
    REFERENCES `flatory`.`storage_files` (`file_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_files_image_albums_file_involves1`
    FOREIGN KEY (`file_involve_id`)
    REFERENCES `flatory`.`file_involves` (`file_involve_id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `flatory`.`tags`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `flatory`.`tags` (
  `tag_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(155) NOT NULL,
  `alias` VARCHAR(155) NOT NULL,
  PRIMARY KEY (`tag_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `flatory`.`files_tags`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `flatory`.`files_tags` (
  `file_id` INT NOT NULL,
  `tag_id` INT NOT NULL,
  PRIMARY KEY (`file_id`, `tag_id`),
  INDEX `fk_files_has_tags_tags1_idx` (`tag_id` ASC),
  INDEX `fk_files_has_tags_files1_idx` (`file_id` ASC),
  CONSTRAINT `fk_files_has_tags_files1`
    FOREIGN KEY (`file_id`)
    REFERENCES `flatory`.`storage_files` (`file_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_files_has_tags_tags1`
    FOREIGN KEY (`tag_id`)
    REFERENCES `flatory`.`tags` (`tag_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `flatory`.`file_categories_proportions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `flatory`.`file_categories_proportions` (
  `file_category_id` INT NOT NULL,
  `proportion_id` INT NOT NULL,
  `is_water_mark` TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`file_category_id`, `proportion_id`),
  INDEX `fk_file_categories_has_proportions_proportions1_idx` (`proportion_id` ASC),
  INDEX `fk_file_categories_has_proportions_file_categories1_idx` (`file_category_id` ASC),
  CONSTRAINT `fk_file_categories_has_proportions_file_categories1`
    FOREIGN KEY (`file_category_id`)
    REFERENCES `flatory`.`file_categories` (`file_category_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_file_categories_has_proportions_proportions1`
    FOREIGN KEY (`proportion_id`)
    REFERENCES `flatory`.`proportions` (`proportion_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `flatory`.`posts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `flatory`.`posts` (
  `post_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_category_id` INT NOT NULL,
  `name` TEXT NOT NULL,
  `content` TEXT NOT NULL,
  `anons` TEXT NULL,
  `alias` VARCHAR(155) NOT NULL,
  `title` TEXT NULL,
  `keywords` TEXT NULL,
  `description` TEXT NULL,
  `created` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` INT NOT NULL DEFAULT 0,
  `file_id` INT NOT NULL DEFAULT 0,
  `status` INT NOT NULL DEFAULT 2 COMMENT 'STATUS_NOT_PUBLISHED = 2;',
  PRIMARY KEY (`post_id`),
  INDEX `fk_content_file_categories1_idx` (`file_category_id` ASC),
  INDEX `fk_posts_files1_idx` (`file_id` ASC),
  CONSTRAINT `fk_content_file_categories1`
    FOREIGN KEY (`file_category_id`)
    REFERENCES `flatory`.`file_categories` (`file_category_id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_posts_files1`
    FOREIGN KEY (`file_id`)
    REFERENCES `flatory`.`storage_files` (`file_id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `flatory`.`posts_tags`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `flatory`.`posts_tags` (
  `post_id` INT UNSIGNED NOT NULL,
  `tag_id` INT NOT NULL,
  PRIMARY KEY (`post_id`, `tag_id`),
  INDEX `fk_content_has_tags_tags1_idx` (`tag_id` ASC),
  INDEX `fk_content_has_tags_content1_idx` (`post_id` ASC),
  CONSTRAINT `fk_content_has_tags_content1`
    FOREIGN KEY (`post_id`)
    REFERENCES `flatory`.`posts` (`post_id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_content_has_tags_tags1`
    FOREIGN KEY (`tag_id`)
    REFERENCES `flatory`.`tags` (`tag_id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `flatory`.`handbks`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `flatory`.`handbks` (
  `handbk_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `alias` VARCHAR(255) NOT NULL,
  `table` VARCHAR(155) NOT NULL,
  `description` TEXT NULL,
  `status` TINYINT NOT NULL DEFAULT 1,
  `adm_url` VARCHAR(155) NULL,
  PRIMARY KEY (`handbk_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `flatory`.`glossary`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `flatory`.`glossary` (
  `glossary_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `alias` VARCHAR(255) NOT NULL,
  `parent_id` INT NOT NULL DEFAULT 0,
  `object_id` INT NOT NULL,
  `handbk_id` INT UNSIGNED NULL,
  `meta_title` TEXT NULL,
  `meta_keywords` TEXT NULL,
  `meta_description` TEXT NULL,
  `updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` TINYINT NOT NULL DEFAULT 1,
  PRIMARY KEY (`glossary_id`),
  INDEX `fk_glossary_handbks1_idx` (`handbk_id` ASC),
  CONSTRAINT `fk_glossary_handbks1`
    FOREIGN KEY (`handbk_id`)
    REFERENCES `flatory`.`handbks` (`handbk_id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `flatory`.`organizations`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `flatory`.`organizations` (
  `organization_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `alias` VARCHAR(255) NOT NULL,
  `params` TEXT NOT NULL,
  `description` TEXT NULL,
  `file_id` INT NULL,
  `created` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`organization_id`),
  INDEX `fk_organizations_files1_idx` (`file_id` ASC),
  CONSTRAINT `fk_organizations_files1`
    FOREIGN KEY (`file_id`)
    REFERENCES `flatory`.`storage_files` (`file_id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `flatory`.`organization_types`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `flatory`.`organization_types` (
  `organization_type_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `params` TEXT NOT NULL DEFAULT '{}',
  PRIMARY KEY (`organization_type_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `flatory`.`organizations_organization_types`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `flatory`.`organizations_organization_types` (
  `organization_type_id` INT UNSIGNED NOT NULL,
  `organization_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`organization_type_id`, `organization_id`),
  INDEX `fk_organization_types_has_organizations_organizations1_idx` (`organization_id` ASC),
  INDEX `fk_organization_types_has_organizations_organization_types1_idx` (`organization_type_id` ASC),
  CONSTRAINT `fk_organization_types_has_organizations_organization_types1`
    FOREIGN KEY (`organization_type_id`)
    REFERENCES `flatory`.`organization_types` (`organization_type_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_organization_types_has_organizations_organizations1`
    FOREIGN KEY (`organization_id`)
    REFERENCES `flatory`.`organizations` (`organization_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `flatory`.`organizations_tags`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `flatory`.`organizations_tags` (
  `tag_id` INT NOT NULL,
  `organization_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`tag_id`, `organization_id`),
  INDEX `fk_tags_has_organizations_organizations1_idx` (`organization_id` ASC),
  INDEX `fk_tags_has_organizations_tags1_idx` (`tag_id` ASC),
  CONSTRAINT `fk_tags_has_organizations_tags1`
    FOREIGN KEY (`tag_id`)
    REFERENCES `flatory`.`tags` (`tag_id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tags_has_organizations_organizations1`
    FOREIGN KEY (`organization_id`)
    REFERENCES `flatory`.`organizations` (`organization_id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `flatory`.`main_object`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `flatory`.`main_object` (
  `id` INT NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `flatory`.`main_objects_organizations`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `flatory`.`main_objects_organizations` (
  `object_id` INT UNSIGNED NOT NULL,
  `organization_id` INT UNSIGNED NOT NULL,
  `organization_type_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`object_id`, `organization_id`, `organization_type_id`),
  INDEX `fk_main_object_has_organizations_organizations1_idx` (`organization_id` ASC),
  INDEX `fk_main_object_has_organizations_main_object1_idx` (`object_id` ASC),
  INDEX `fk_main_object_organizations_organization_types1_idx` (`organization_type_id` ASC),
  CONSTRAINT `fk_main_object_has_organizations_main_object1`
    FOREIGN KEY (`object_id`)
    REFERENCES `flatory`.`main_object` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_main_object_has_organizations_organizations1`
    FOREIGN KEY (`organization_id`)
    REFERENCES `flatory`.`organizations` (`organization_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_main_object_organizations_organization_types1`
    FOREIGN KEY (`organization_type_id`)
    REFERENCES `flatory`.`organization_types` (`organization_type_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
