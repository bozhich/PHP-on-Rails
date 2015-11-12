<?php

class Migration_CreateLanguagesFile extends Core_Migration_Abstract  {

	const NAME = 'CreateLanguages';
	const CREATED_AT = '1445973523';
	const CREATED_IN_CFG = 'luka';

	public function up() {
		$query = "CREATE TABLE `languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `language` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `code` varchar(5) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
		return $query;
	}

	public function down() {
		$query = "DROP TABLE IF EXISTS `languages`";
		return $query;
	}
}