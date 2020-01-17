CREATE DATABASE `AgileOctopus`;

CREATE USER 'OctopusUser' @'localhost' IDENTIFIED BY 'xxxxA password better than this.xxxx';

GRANT ALL PRIVILEGES ON AgileOctopus.* TO 'OctopusUser' @'localhost';

FLUSH PRIVILEGES;

USE 'AgileOctopus';

CREATE TABLE `ElectricPrices` (
  `entry_id` int(11) NOT NULL AUTO_INCREMENT,
  `valid_from` datetime NOT NULL COMMENT 'UTC+0',
  `valid_to` datetime NOT NULL COMMENT 'UTC+0',
  `value_exc_vat` decimal(7, 4) NOT NULL COMMENT 'GBp',
  `value_inc_vat` decimal(7, 4) NOT NULL COMMENT 'GBp',
  PRIMARY KEY (`entry_id`)
);

CREATE TABLE `ElectricConsumption` (
  `entry_id` int(11) NOT NULL AUTO_INCREMENT,
  `interval_start` datetime NOT NULL COMMENT 'UTC+0',
  `interval_end` datetime NOT NULL COMMENT 'UTC+0',
  `consumption` decimal(5, 3) NOT NULL COMMENT 'kWh',
  PRIMARY KEY (`entry_id`)
);

CREATE TABLE `StandingCharges` (
  `entry_id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `value_exc_vat` decimal(7, 4) NOT NULL COMMENT 'GBp',
  `value_inc_vat` decimal(7, 4) NOT NULL COMMENT 'GBp',
  PRIMARY KEY (`entry_id`)
);