<?php

class Migration_CreateTranslateFile extends Core_Migration_Abstract  {

	const NAME = 'CreateTranslate';
	const CREATED_AT = '1445973523';
	const CREATED_IN_CFG = 'luka';

	public function up() {
		$query = "CREATE TABLE `translate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `tag_hash` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_bin,
  `language_code` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag_hash` (`tag_hash`) USING BTREE,
  UNIQUE KEY `tag_hash_2` (`tag_hash`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=642 DEFAULT CHARSET=utf8";
		return $query;
	}

	public function down() {
		$query = "DROP TABLE IF EXISTS `translate`";
		return $query;
	}
}