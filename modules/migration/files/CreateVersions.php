<?php

class Migration_CreateVersionsFile extends Core_Migration_Abstract  {

	const NAME = 'CreateVersions';
	const CREATED_AT = '1445973523';
	const CREATED_IN_CFG = 'luka';

	public function up() {
		$query = "CREATE TABLE `versions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `url` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `key` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `version` int(10) unsigned DEFAULT NULL,
  `is_dev` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `version` (`version`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
		return $query;
	}

	public function down() {
		$query = "DROP TABLE IF EXISTS `versions`";
		return $query;
	}
}