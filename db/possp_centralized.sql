/*
Navicat MySQL Data Transfer

Source Server         : Localhos(XAMPP)
Source Server Version : 50614
Source Host           : localhost:3306
Source Database       : possp

Target Server Type    : MYSQL
Target Server Version : 50614
File Encoding         : 65001

Date: 2014-04-30 16:16:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `ospos_locations`
-- ----------------------------
DROP TABLE IF EXISTS `ospos_locations`;
CREATE TABLE `ospos_locations` (
  `name` varchar(50) NOT NULL,
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `hostname` varchar(50) NOT NULL DEFAULT 'localhost',
  `username` varchar(20) NOT NULL DEFAULT 'root',
  `password` varchar(200) NOT NULL,
  `database` varchar(20) NOT NULL,
  `dbdriver` varchar(12) NOT NULL DEFAULT 'mysql',
  `dbprefix` varchar(10) NOT NULL DEFAULT 'ospos_',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ospos_locations
-- ----------------------------
-- INSERT INTO `ospos_locations` VALUES ('otra', NULL, 'localhost', 'root', 'root', 'possp2', 'mysql', 'ospos_', '1');

-- ----------------------------
-- Table structure for `ospos_transfers`
-- ----------------------------
DROP TABLE IF EXISTS `ospos_transfers`;
CREATE TABLE `ospos_transfers` (
  `transfer_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender` varchar(20) NOT NULL,
  `receiver` varchar(20) NOT NULL,
  `date` date DEFAULT NULL,
  `payment_type` varchar(512) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `comment` text NOT NULL,
  PRIMARY KEY (`transfer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ospos_transfers
-- ----------------------------

-- ----------------------------
-- Table structure for `ospos_transfer_items`
-- ----------------------------
DROP TABLE IF EXISTS `ospos_transfer_items`;
CREATE TABLE `ospos_transfer_items` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `transfer_id` int(11) unsigned NOT NULL,
  `item_id` int(10) NOT NULL,
  `serialnumber` varchar(30) DEFAULT NULL,
  `item_cost_price` decimal(15,2) NOT NULL,
  `item_unit_price` double(15,2) NOT NULL,
  `discount_percent` int(11) NOT NULL,
  `quantity_purchased` double(15,0) NOT NULL DEFAULT '1',
  `line` int(3) NOT NULL,
  `description` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for `ospos_orders`
-- ----------------------------
DROP TABLE IF EXISTS `ospos_orders`;
CREATE TABLE `ospos_orders` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `employee_id` int(10) DEFAULT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `comments` mediumtext,
  `location` varchar(20) DEFAULT NULL,
  `status` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `ospos_order_items`;
CREATE TABLE `ospos_order_items` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_item` int(10) DEFAULT NULL,
  `current_quantity` double(15,0) DEFAULT NULL,
  `quantity` double(15,0) DEFAULT NULL,
  `id_order` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for `ospos_chat`
-- ----------------------------
DROP TABLE IF EXISTS `ospos_chat`;
CREATE TABLE `ospos_chat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from_id` int(10) unsigned NOT NULL,
  `to_id` int(10) unsigned NOT NULL,
  `message` text NOT NULL,
  `sent` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `recd` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `to` (`to_id`),
  KEY `from` (`from_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ospos_chat
-- ----------------------------

-- ----------------------------
-- Table structure for `ospos_chat_status`
-- ----------------------------
DROP TABLE IF EXISTS `ospos_chat_status`;
CREATE TABLE `ospos_chat_status` (
  `id` tinyint(3) unsigned NOT NULL,
  `name` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ospos_chat_status
-- ----------------------------
INSERT INTO `ospos_chat_status` VALUES ('0', 'offline');
INSERT INTO `ospos_chat_status` VALUES ('1', 'online');
INSERT INTO `ospos_chat_status` VALUES ('2', 'iddle');

-- ----------------------------
-- Table structure for `ospos_chat_users`
-- ----------------------------
DROP TABLE IF EXISTS `ospos_chat_users`;
CREATE TABLE `ospos_chat_users` (
  `chat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `location` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `status` int(5) NOT NULL,
  `disabled` tinyint(4) NOT NULL,
  `last_action` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`chat_id`),
  UNIQUE KEY `usr` (`user_id`,`location`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ospos_chat_users
-- ----------------------------

-- ----------------------------
-- Table structure for `ospos_chat_user_typing`
-- ----------------------------
DROP TABLE IF EXISTS `ospos_chat_user_typing`;
CREATE TABLE `ospos_chat_user_typing` (
  `from_id` int(11) NOT NULL,
  `to_id` int(11) NOT NULL,
  `typing` bit(1) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`from_id`,`to_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ospos_chat_user_typing
-- ----------------------------

-- ----------------------------
-- View structure for `ospos_chat_users_view`
-- ----------------------------
DROP VIEW IF EXISTS `ospos_chat_users_view`;
CREATE VIEW `ospos_chat_users_view` AS select `ospos_chat_users`.`chat_id` AS `chat_id`,`ospos_chat_users`.`user_id` AS `user_id`,concat(`ospos_chat_users`.`username`,' (',`ospos_chat_users`.`location`,')') AS `user`,`ospos_chat_users`.`location` AS `location`,`ospos_chat_users`.`username` AS `username`,if(`ospos_chat_users`.`disabled`,0,`ospos_chat_users`.`status`) AS `status_id`,`ospos_chat_status`.`name` AS `status_name`,`ospos_chat_users`.`disabled` AS `disabled`,`ospos_chat_users`.`last_action` AS `last_action` from (`ospos_chat_users` join `ospos_chat_status`) where (if(`ospos_chat_users`.`disabled`,0,`ospos_chat_users`.`status`) = `ospos_chat_status`.`id`) ;

-- ----------------------------
-- View structure for `ospos_chat_view`
-- ----------------------------
DROP VIEW IF EXISTS `ospos_chat_view`;
CREATE VIEW `ospos_chat_view` AS select `ospos_chat`.`id` AS `id`,`ospos_chat`.`from_id` AS `from_id`,concat(`a`.`username`,' (',`a`.`location`,')') AS `from`,`ospos_chat`.`to_id` AS `to_id`,concat(`b`.`username`,' (',`b`.`location`,')') AS `to`,`ospos_chat`.`message` AS `message`,`ospos_chat`.`sent` AS `sent`,`ospos_chat`.`recd` AS `recd` from ((`ospos_chat` join `ospos_chat_users` `a`) join `ospos_chat_users` `b`) where ((`ospos_chat`.`from_id` = `a`.`chat_id`) and (`ospos_chat`.`to_id` = `b`.`chat_id`)) ;
