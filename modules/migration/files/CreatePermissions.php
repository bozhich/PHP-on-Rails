<?php

class Migration_CreatePermissionsFile extends Core_Migration_Abstract  {

	const NAME = 'CreatePermissions';
	const CREATED_AT = '1445973523';
	const CREATED_IN_CFG = 'luka';

	public function up() {
		$query = "CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `module` varchar(255) DEFAULT 'default',
  `lang` varchar(10) DEFAULT NULL,
  `controller` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `bit_flag` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8";
		return $query;
	}

	public function down() {
		$query = "DROP TABLE IF EXISTS `permissions`";
		return $query;
	}
}