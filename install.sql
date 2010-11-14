-- Uncomment the following line to create a default database
-- CREATE DATABASE `evilcode_services` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;

-- Language database
CREATE TABLE `evilcode_services`.`language` (
	`languageID` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`name` VARCHAR( 255 ) NOT NULL ,
	`code` VARCHAR( 255 ) NOT NULL
) ENGINE = MYISAM ;