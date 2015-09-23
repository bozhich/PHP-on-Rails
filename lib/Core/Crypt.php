<?php

/**
 * Class Core_Crypt
 */
class Core_Crypt {
	const ADDITIONAL_KEY = 'CHUCK_NORRIS';

	const SALT_LENGTH = 10;


	/**
	 * @param $data
	 * @param $key
	 * @return string
	 */
	public static function generete($data, $key) {
		$key .= self::ADDITIONAL_KEY;

		$string = serialize($data);
		$salt = substr(md5($string), 0, self::SALT_LENGTH);

		$result = '';

		for ($i = 0; $i < strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key)) - 1, 1);
			$char = chr(ord($char) + ord($keychar));
			$result .= $char;
		}

		return $salt . strtr(base64_encode($result), '+/=', '-_,');
	}


	/**
	 * @param $string
	 * @param $key
	 * @return bool|mixed
	 */
	public static function parse($string, $key) {
		$key .= self::ADDITIONAL_KEY;

		$salt = substr($string, 0, self::SALT_LENGTH);
		$string = substr($string, self::SALT_LENGTH);

		$result = '';

		$string = base64_decode(strtr($string, '-_,', '+/='));

		for ($i = 0; $i < strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key)) - 1, 1);
			$char = chr(ord($char) - ord($keychar));
			$result .= $char;
		}

		if (substr(md5($result), 0, self::SALT_LENGTH) == $salt) {
			return unserialize($result);
		} else {
			return false;
		}
	}
}
