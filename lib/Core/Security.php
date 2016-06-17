<?php

/**
 * Class Core_Security
 */
class Core_Security {
	/**
	 *
	 */
	const SALT = '';

	/**
	 * @param $pass
	 * @return string
	 */
	public static function generate($pass) {
		return md5($pass . self::SALT);
	}


	/**
	 * @param $pass
	 * @param $hash
	 * @return bool
	 */
	public static function check($pass, $hash) {
		return ($hash == md5($pass . self::SALT));
	}
}
