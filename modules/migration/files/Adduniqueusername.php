<?php

class Migration_AdduniqueusernameFile extends Core_Migration_Abstract  {

	const NAME = 'Adduniqueusername';
	const CREATED_AT = '1445981638';
	const CREATED_IN_CFG = 'luka';

	public function up() {
		$query = "ALTER TABLE `panel_users`
ADD UNIQUE INDEX (`user`) USING BTREE ;

";
		return $query;
	}

	public function down() {
		$query = "";
		return $query;
	}
}