<?php

class Migration_AddindextotranslateFile extends Core_Migration_Abstract {

	const NAME = 'Addindextotranslate';
	const CREATED_AT = '1445973529';
	const CREATED_IN_CFG = 'luka';

	public function up() {
		$query = "ALTER TABLE `translate`
ADD UNIQUE INDEX (`tag_hash`) USING BTREE ;";

		return $query;
	}

	public function down() {
		$query = "//__DOWN_ACTION__";

		return $query;
	}
}