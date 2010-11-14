-- Uncomment the following lines to create a default database
-- CREATE DATABASE `evilcode_services` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
-- USE `evilcode_services`;

-- Language database
CREATE TABLE `evilcode_services`.`language` (
	`languageID` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`name` VARCHAR( 255 ) NOT NULL ,
	`code` VARCHAR( 255 ) NOT NULL
) ENGINE = MYISAM ;

INSERT INTO 
	`language` (`languageID`, `name`, `code`) 
VALUES 
	(NULL, 'English', 'en'),
	(NULL, 'Deutsch', 'de');
	
-- Language item database
CREATE TABLE `evilcode_services`.`language_item` (
	`itemID` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`languageID` INT NOT NULL ,
	`name` VARCHAR( 255 ) NOT NULL ,
	`value` TEXT NOT NULL
) ENGINE = MYISAM ;