<?php

class Default_LogsModel extends Core_Model {
	protected static $table = 'login_history';

	const USER_LOGIN = 1;

	protected static $logs_maping = array(
		self::USER_LOGIN => 'login_history',
	);

	public static function insert($data, $log_type) {
		return Core_Db::insert(self::getTable($log_type), $data)->execute();
	}

	protected static function getTable($log_type) {
		return self::$logs_maping[$log_type];
	}


	public static function getLogsList($where, $what) {
		return Core_Db::select('*')->from(self::getTable($what))->where($where)->fetchAll();
	}


	public static function getStats($group) {
		return Core_Db::select('COUNT(*)')->as('count')
						->select('date(timestamp)')->as('date')
						->from(self::$table)
						->where('month(timestamp) = month(now())')
						->groupBy($group)->fetchAssoc('date');
	}

	public static function getUserLastLogin($user_id) {
		$last_login_rs = Core_Db::select('timestamp')
			->from(static::$table)
			->where('user_id', '=', $user_id)
			->orderBy('timestamp', 'DESC')
			->fetch();

		if (!$last_login_rs) {
			return false;
		}

		return $last_login_rs->timestamp;
	}
}
