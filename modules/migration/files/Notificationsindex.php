<?php

class Migration_NotificationsindexFile extends Core_Migration_Abstract  {

	const NAME = 'Notificationsindex';
	const CREATED_AT = 1452093368;
	const CREATED_IN_CFG = 'luka';

	public function up() {
		$query = "ALTER TABLE `notifications`
ADD INDEX (`id`) ,
ADD INDEX (`user_id`) ,
ADD INDEX (`author_id`) ,
ADD INDEX (`item_id`) ;";
		return $query;
	}

	public function down() {
		$query = "//__DOWN_ACTION__";
		return $query;
	}
}
