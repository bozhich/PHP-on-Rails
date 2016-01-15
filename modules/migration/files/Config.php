<?php

class Migration_ConfigFile extends Core_Migration_Abstract  {

	const NAME = 'Config';
	const CREATED_AT = 1451398016;
	const CREATED_IN_CFG = 'luka';

	public function up() {
		$query = "CREATE TABLE `config` (
						`id`  int(10) NOT NULL AUTO_INCREMENT ,
						`name`  varchar(255) NULL ,
						`value`  varchar(255) NULL ,
						PRIMARY KEY (`id`)
					);";
		return $query;
	}

	public function down() {
		$query = "//__DOWN_ACTION__";
		return $query;
	}
}