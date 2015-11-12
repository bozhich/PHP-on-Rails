<?php

class Migration_InsertbasepermissionsFile extends Core_Migration_Abstract  {

	const NAME = 'Insertbasepermissions';
	const CREATED_AT = '1446195740';
	const CREATED_IN_CFG = 'luka';

	public function up() {
		$query = "INSERT INTO `permissions`
					VALUES
						(
							'1',
							'Index : Index',
							'admin',
							NULL,
							'index',
							'index',
							'0'
						),
						(
							'2',
							'Navigation : Index',
							'admin',
							NULL,
							'navigation',
							'index',
							'0'
						),
						(
							'3',
							'Index : Load',
							'admin',
							NULL,
							'index',
							'load',
							'0'
						),
						(
							'4',
							'Session : Index',
							'admin',
							NULL,
							'session',
							'index',
							'0'
						)
						(
							'5',
							'Session : Logout',
							'admin',
							NULL,
							'session',
							'logout',
							'0'
						);";
		return $query;
	}

	public function down() {
		$query = "//__DOWN_ACTION__";
		return $query;
	}
}