<?php

class Admin_TranslateModel extends Core_Model {
	protected static $table = 'translate';


	/**
	 * @param $data
	 * @return array
	 */
	public static function g2etAll($data) {
		return self::select('*')->from(static::$table)->where($data)->fetchAll();
	}


}
