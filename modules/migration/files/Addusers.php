<?php

class Migration_AddusersFile extends Core_Migration_Abstract {

	const NAME = 'Addusers';
	const CREATED_AT = '1445973530';
	const CREATED_IN_CFG = 'luka';

	public function up() {
		$query = "INSERT INTO `panel_users` (`user`, `email`, `password`, `role_id`, `is_developer`, `company`, `language_code`)
								VALUES (
								'lukas', 'email1', '" . Core_Security::generate('osiris') . "', '1', '1', 'rofl', 'en_EN'),
								('tim', 'email2', '" . Core_Security::generate('123456') . "', '1', '1', 'rofl', 'en_EN'
								)";

		return $query;
	}

	public function down() {
		$query = "";

		return $query;
	}
}