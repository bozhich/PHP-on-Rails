<?php

class Admin_LogsModel extends Core_Model {
	const USER_LOGIN = 1;

	protected static $logs_maping = array(
		self::USER_LOGIN => 'panel_login_history',
	);

	public static function insert($data, $log_type) {
		return dibi::insert(self::getTable($log_type), $data)->execute();
	}

	protected static function getTable($log_type) {
		return self::$logs_maping[$log_type];
	}
}
