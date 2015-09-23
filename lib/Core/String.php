<?php

/**
 * Class Core_String
 */
class Core_String {
	/**
	 * @param $word
	 * @return string
	 */
	public static function toClass($word) {
		//return str_replace(' ', '', ucwords(preg_replace('/[^A-Z^a-z^0-9]+/', ' ', $word)));

		$class_name = '';
		foreach (explode('-', $word) as $w_part) {
			$class_name .= strtoupper(substr($w_part, 0, 1)) . substr($w_part, 1);
		}

		return $class_name;
	}


	/**
	 * @param $word
	 * @return string
	 */
	public static function toFunction($word) {
		$word = self::toClass($word);

		return strtolower(substr($word, 0, 1)) . substr($word, 1);
	}


	/**
	 * @param        $string
	 * @param int    $length
	 * @param string $etc
	 * @param bool   $break_words
	 * @param bool   $middle
	 * @return mixed|string
	 */
	public static function truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false) {
		if ($length == 0) {
			return '';
		}

		if (strlen($string) > $length) {
			$length -= min($length, strlen($etc));
			if (!$break_words && !$middle) {
				$string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length + 1));
			}
			if (!$middle) {
				return substr($string, 0, $length) . $etc;
			} else {
				return substr($string, 0, $length / 2) . $etc . substr($string, -$length / 2, null);
			}
		} else {
			return $string;
		}
	}


	/**
	 * @param $var
	 * @return string
	 */
	public static function safe($var) {
		if (is_array($var)) {
			foreach ($var as &$value) {
				$value = self::safe($value);
			}
		} elseif (is_string($var)) {
			$var = trim($var);
			$var = htmlspecialchars($var);
		} else {
			$var = intval($var);
		}

		return $var;
	}


	/**
	 * @param $str
	 * @return string
	 */
	public static function crypt($str) {
		return md5($str);
	}
}