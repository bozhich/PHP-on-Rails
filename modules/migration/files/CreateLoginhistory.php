<?php

class Migration_CreateLoginhistoryFile extends Core_Migration_Abstract  {

	const NAME = 'CreateLoginhistory';
	const CREATED_AT = 1445792215;
	const CREATED_IN_CFG = 'luka';

	public function up() {
		$query = "CREATE TABLE `login_history` (
  `id` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `user_id` int(10) DEFAULT NULL,
  `ip` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
		return $query;
	}

	public function down() {
		$query = "DROP TABLE IF EXISTS `login_history`";
		return $query;
	}
}