<?php

class Migration_CreateTranslateFile extends Core_Migration_Abstract  {

	const NAME = 'CreateTranslate';
	const CREATED_AT = 1445614722;
	const CREATED_IN_CFG = 'luka';

	public function up() {
		$query = "CREATE TABLE `translate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) DEFAULT NULL,
  `tag_hash` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `language_code` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `not_found_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag_hash` (`tag_hash`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3588 DEFAULT CHARSET=utf8";
		return $query;
	}

	public function down() {
		$query = "DROP TABLE IF EXISTS `translate`";
		return $query;
	}
}