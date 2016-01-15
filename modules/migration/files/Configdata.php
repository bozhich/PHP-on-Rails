<?php

class Migration_ConfigdataFile extends Core_Migration_Abstract  {

	const NAME = 'Configdata';
	const CREATED_AT = 1451401919;
	const CREATED_IN_CFG = 'luka';

	public function up() {
		$query = "INSERT INTO `config` (`name`, `value`) VALUES ('dev_mode', '1'), ('maintenance', '1')";
		return $query;
	}

	public function down() {
		$query = "//__DOWN_ACTION__";
		return $query;
	}
}