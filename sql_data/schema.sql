SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for translate
-- ----------------------------
DROP TABLE IF EXISTS `translate`;
CREATE TABLE `translate` (
  `tag` varchar(255) DEFAULT NULL,
  `tag_hash` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `language_code` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
