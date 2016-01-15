<?php

class Migration_ConfigalterFile extends Core_Migration_Abstract  {

	const NAME = 'Configalter';
	const CREATED_AT = 1451398240;
	const CREATED_IN_CFG = 'luka';

	public function up() {
		$query = "ALTER TABLE `config` MODIFY COLUMN `value` enum('0','1') NULL DEFAULT NULL AFTER `name`;";
		return $query;
	}

	public function down() {
		$query = "//__DOWN_ACTION__";
		return $query;
	}
}