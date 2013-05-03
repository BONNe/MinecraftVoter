/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50516
Source Host           : localhost:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50516
File Encoding         : 65001

Date: 2012-08-22 11:06:14
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `voter_clicks`
-- ----------------------------
DROP TABLE IF EXISTS `voter_clicks`;
CREATE TABLE `voter_clicks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_key` varchar(50) NOT NULL,
  `ip` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `time_added` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of voter_clicks
-- ----------------------------
INSERT INTO `voter_clicks` VALUES ('12', 'wos', '127.0.0.1', 'Test :)', '1340792853');
