/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50614
Source Host           : localhost:3306
Source Database       : possp

Target Server Type    : MYSQL
Target Server Version : 50614
File Encoding         : 65001

Date: 2014-08-27 11:28:46
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `ospos_service_log`
-- ----------------------------
DROP TABLE IF EXISTS `ospos_service_log`;
CREATE TABLE `ospos_service_log` (
  `service_id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) NOT NULL,
  `serial` varchar(18) DEFAULT NULL,
  `model_id` int(11) NOT NULL,
  `date_received` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_delivered` datetime DEFAULT NULL,
  `problem` mediumtext,
  `status` int(1) NOT NULL DEFAULT '1',
  `deleted` int(1) DEFAULT '0',
  `color` varchar(50) DEFAULT NULL,
  `add_pay` varchar(512) DEFAULT '0',
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ospos_service_log
-- ----------------------------
INSERT INTO `ospos_service_log` VALUES ('1', '2', '1234567890', '8', '2014-08-12 21:51:01', null, 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.', '2', '0', 'Whitle', 'Cash 1,GiftCard 1,DebitCard 1,Check 1,CreditCard 1');
INSERT INTO `ospos_service_log` VALUES ('2', '5', '09876543221', '6', '2014-08-12 21:52:47', null, 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.', '1', '0', 'Red', 'Cash 1,GiftCard 1,DebitCard 1,Check 1,CreditCard 1');
INSERT INTO `ospos_service_log` VALUES ('3', '3', '34345566878787979', '19', '2014-08-12 21:54:42', null, 'I have a problem with my phone and I really do not know ', '1', '0', 'Green', 'Cash 1,GiftCard 1,DebitCard 1,Check 1,CreditCard 1');
INSERT INTO `ospos_service_log` VALUES ('4', '2', 'Custom', '0', '2014-08-12 22:09:25', null, 'Cracked screeen', '1', '0', 'Black', 'Cash 1,GiftCard 1,DebitCard 1,Check 1,CreditCard 1');
INSERT INTO `ospos_service_log` VALUES ('5', '6', '015735825845926', '0', '2014-08-12 22:10:39', null, 'Not charging', '1', '0', 'White', 'Cash 1,GiftCard 1,DebitCard 1,Check 1,CreditCard 1');
INSERT INTO `ospos_service_log` VALUES ('6', '2', '1234567890', '19', '2014-08-13 12:12:39', null, 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.', '1', '0', 'black', 'Cash 1,GiftCard 1,DebitCard 1,Check 1,CreditCard 1');
