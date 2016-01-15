<?php

class Migration_CreateEventsFile extends Core_Migration_Abstract  {

	const NAME = 'CreateEvents';
	const CREATED_AT = 1445792215;
	const CREATED_IN_CFG = 'luka';

	public function up() {
		$query = "CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `json_data` text,
  `execution_time` timestamp NULL DEFAULT NULL,
  `priority` int(11) DEFAULT '0',
  `status` int(11) NOT NULL,
  `executed_at` timestamp NULL DEFAULT NULL,
  `added_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `added_by` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'system' COMMENT 'different than system when adding via admin panel',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
		return $query;
	}

	public function down() {
		$query = "DROP TABLE IF EXISTS `events`";
		return $query;
	}
}