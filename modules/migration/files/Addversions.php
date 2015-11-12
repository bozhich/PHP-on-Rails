<?php

class Migration_AddversionsFile extends Core_Migration_Abstract {

	const NAME = 'Addversions';
	const CREATED_AT = '1445973526';
	const CREATED_IN_CFG = 'luka';

	public function up() {
		$query = "INSERT INTO `versions` (`name`, `url`, `is_dev`) VALUES ('dev', '" . cfg()->remote_site_url . "', '1')";

		return $query;
	}

	public function down() {
		$query = "//__DOWN_ACTION__";

		return $query;
	}
}