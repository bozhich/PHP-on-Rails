<?php

/**
 * Class Core_Server
 */
class Core_Server {
	const LOAD_FILE = '/proc/loadavg';
	const CRITICAL_LOAD = 10;


	/**
	 * @return array|bool|string
	 */
	public static function getLoad() {
		if (!Core_Cache::get('server_load')) {
			if (is_file(self::LOAD_FILE)) {
				$file_source = (double) Core_Files::getContent(self::LOAD_FILE);
				list($server_load) = explode(' ', $file_source);
				Core_Cache::set('server_load', $server_load, 60);
			}
		}

		return Core_Cache::get('server_load');
	}


	/**
	 * @return bool
	 */
	public static function isCriticalLoad() {
		return self::getLoad() > self::CRITICAL_LOAD;
	}
}
