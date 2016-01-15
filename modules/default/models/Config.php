<?php

class Default_ConfigModel extends Core_Model {
	protected static $table = 'config';

	public static function getAll($data) {
		$cache_name = self::getCacheName();
		$cache_data = Core_Cache::get($cache_name);
		if (!$cache_data) {
			$data = Core_Db::select('*')->from(static::$table)->fetchPairs('name', 'value');
			Core_Cache::set($cache_name, $data, time() + mt_rand(4500, 6000));

			return $data;
		}

		return Core_Cache::get($cache_name);
	}


}