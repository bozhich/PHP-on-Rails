<?php

class Migration_CreatePanelrolesFile extends Core_Migration_Abstract  {

	const NAME = 'CreatePanelroles';
	const CREATED_AT = '1445973523';
	const CREATED_IN_CFG = 'luka';

	public function up() {
		$query = "CREATE TABLE `panel_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `permissions` int(20) DEFAULT NULL,
  `is_owner` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8";
		return $query;
	}

	public function down() {
		$query = "DROP TABLE IF EXISTS `panel_roles`";
		return $query;
	}
}