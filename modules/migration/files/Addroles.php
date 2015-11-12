<?php

class Migration_AddrolesFile extends Core_Migration_Abstract {

	const NAME = 'Addroles';
	const CREATED_AT = '1445973533';
	const CREATED_IN_CFG = 'luka';

	public function up() {
		$query = "INSERT INTO `panel_roles` (`id`, `name`, `is_owner`) VALUES (1, 'Owner', '1'), (2, 'admin', '0'), (3, 'Клош', '1')";

		return $query;
	}

	public function down() {
		$query = "";

		return $query;
	}
}