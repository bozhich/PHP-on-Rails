<?php

class Migration_CreatePanelusersFile extends Core_Migration_Abstract  {

	const NAME = 'CreatePanelusers';
	const CREATED_AT = '1445973523';
	const CREATED_IN_CFG = 'luka';

	public function up() {
		$query = "CREATE TABLE `panel_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `role_id` int(10) DEFAULT NULL,
  `last_active` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `session_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `is_developer` int(1) DEFAULT '0',
  `company` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `language_code` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
		return $query;
	}

	public function down() {
		$query = "DROP TABLE IF EXISTS `panel_users`";
		return $query;
	}
}