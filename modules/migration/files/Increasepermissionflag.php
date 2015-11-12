<?php

class Migration_IncreasepermissionflagFile extends Core_Migration_Abstract  {

	const NAME = 'Increasepermissionflag';
	const CREATED_AT = '1446206690';
	const CREATED_IN_CFG = 'luka';

	public function up() {
		$query = "ALTER TABLE `permissions` MODIFY COLUMN `bit_flag`  bigint(20) NULL DEFAULT NULL AFTER `action`";
		return $query;
	}

	public function down() {
		$query = "//__DOWN_ACTION__";
		return $query;
	}
}