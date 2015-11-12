<?php

class Migration_AdduniqueemailFile extends Core_Migration_Abstract  {

	const NAME = 'Adduniqueemail';
	const CREATED_AT = '1445981744';
	const CREATED_IN_CFG = 'luka';

	public function up() {
		$query = "ALTER TABLE `panel_users`
ADD UNIQUE INDEX (`email`) USING BTREE ;

";
		return $query;
	}

	public function down() {
		$query = "//__DOWN_ACTION__";
		return $query;
	}
}