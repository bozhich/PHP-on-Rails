<?php

/**
 * Class Core_String
 */
class Core_String {
	private static $bg_to_en_letters = array(
		'А'                 => 'a',
		'Б'                 => 'b',
		'В'                 => 'v',
		'Г'                 => 'g',
		'Д'                 => 'd',
		'Е'                 => 'e',
		'Ж'                 => 'zh',
		'З'                 => 'z',
		'И'                 => 'i',
		'Й'                 => 'y',
		'К'                 => 'k',
		'Л'                 => 'l',
		'М'                 => 'm',
		'Н'                 => 'n',
		'О'                 => 'o',
		'П'                 => 'p',
		'Р'                 => 'r',
		'С'                 => 's',
		'Т'                 => 't',
		'У'                 => 'u',
		'Ф'                 => 'f',
		'Х'                 => 'h',
		'Ц'                 => 'tz',
		'Ч'                 => 'ch',
		'Ш'                 => 'sh',
		'Щ'                 => 'sht',
		'Ь'                 => 'u',
		'Ъ'                 => 'u',
		'Ю'                 => 'yu',
		'Я'                 => 'ya',
		'а'                 => 'a',
		'б'                 => 'b',
		'в'                 => 'v',
		'г'                 => 'g',
		'д'                 => 'd',
		'е'                 => 'e',
		'ж'                 => 'zh',
		'з'                 => 'z',
		'и'                 => 'i',
		'й'                 => 'y',
		'к'                 => 'k',
		'л'                 => 'l',
		'м'                 => 'm',
		'н'                 => 'n',
		'о'                 => 'o',
		'п'                 => 'p',
		'р'                 => 'r',
		'с'                 => 's',
		'т'                 => 't',
		'у'                 => 'u',
		'ф'                 => 'f',
		'х'                 => 'h',
		'ц'                 => 'tz',
		'ч'                 => 'ch',
		'ш'                 => 'sh',
		'щ'                 => 'sht',
		'ь'                 => 'u',
		'ъ'                 => 'u',
		'ю'                 => 'yu',
		'я'                 => 'ya',
		'[!@\#$%^&*()><_ ]' => '-',
		'--'                => '-',
	);

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


	/**
	 * @param $str
	 * @return string
	 */
	public static function slug($str) {
		$str = trim($str);
		foreach (self::$bg_to_en_letters as $search => $replace) {
			$str = preg_replace('#' . $search . '#', $replace, $str);
		}

		$str = preg_replace('# #', '-', $str);
		$str = preg_replace('#_#', '-', $str);
		return strtolower($str);
	}
}
