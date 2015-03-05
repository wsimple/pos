/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50614
Source Host           : localhost:3306
Source Database       : possp

Target Server Type    : MYSQL
Target Server Version : 50614
File Encoding         : 65001

Date: 2014-10-06 11:37:15
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `ospos_credits`
-- ----------------------------
DROP TABLE IF EXISTS `ospos_credits`;
CREATE TABLE `ospos_credits` (
  `credit_id` int(11) NOT NULL AUTO_INCREMENT,
  `person_custo_id` int(11) DEFAULT NULL,
  `transfer_id` int(11) DEFAULT NULL,
  `person_emplo_id` int(11) DEFAULT NULL,
  `payment_amount` decimal(15,2) DEFAULT NULL,
  `balance` decimal(15,2) DEFAULT NULL,
  `payment_period` varchar(255) DEFAULT NULL,
  `day_pay` date DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`credit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=192 DEFAULT CHARSET=latin1;

