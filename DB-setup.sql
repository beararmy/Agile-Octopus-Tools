CREATE DATABASE `AgileOctopus`;
CREATE USER 'OctopusUser'@'localhost' IDENTIFIED BY 'xxxxA password better than this.xxxx';
GRANT ALL PRIVILEGES ON AgileOctopus.*  TO 'OctopusUser'@'localhost';
FLUSH PRIVILEGES;
USE 'AgileOctopus';
CREATE TABLE `ElectricPrices` (
        `entry_id` INT NOT NULL AUTO_INCREMENT,
        `valid_from` DATETIME NOT NULL COMMENT 'UTC+0',
        `valid_to` DATETIME NOT NULL COMMENT 'UTC+0',
        `value_exc_vat` DECIMAL(7,4) NOT NULL COMMENT 'GBp',
        `value_inc_vat` DECIMAL(7,4) NOT NULL COMMENT 'GBp',
        PRIMARY KEY (`entry_id`)
);
CREATE TABLE `ElectricConsumption` (
        `entry_id` INT NOT NULL AUTO_INCREMENT,
        `interval_start` DATETIME NOT NULL COMMENT 'UTC+0',
        `interval_end` DATETIME NOT NULL COMMENT 'UTC+0',
        `consumption` DECIMAL(5,3) NOT NULL COMMENT 'kWh',
        PRIMARY KEY (`entry_id`)
);