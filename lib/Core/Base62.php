<?php

/**
 * Class Core_Base62
 */
class Core_Base62 {

	/**
	 * Converts a base 10 number to any other base.
	 *
	 * @param int    $val Decimal number
	 * @param int    $base Base to convert to. If null, will use strlen($chars) as base.
	 * @param string $chars Characters used in base, arranged lowest to highest. Must be at least $base characters long.
	 *
	 * @return string    Number converted to specified base
	 */
	public static function encode($val, $base = 62, $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
		if (!isset($base)) $base = strlen($chars);
		$str = '';
		do {
			$m = bcmod($val, $base);
			$str = $chars[$m] . $str;
			$val = bcdiv(bcsub($val, $m), $base);
		} while (bccomp($val, 0) > 0);

		return $str;
	}

	/**
	 * Convert a number from any base to base 10
	 *
	 * @param string $str Number
	 * @param int    $base Base of number. If null, will use strlen($chars) as base.
	 * @param string $chars Characters use in base, arranged lowest to highest. Must be at least $base characters long.
	 *
	 * @return int    Number converted to base 10
	 */
	public static function decode($str, $base = 62, $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
		if (!isset($base)) $base = strlen($chars);
		$len = strlen($str);
		$val = 0;
		$arr = array_flip(str_split($chars));
		for ($i = 0; $i < $len; ++$i) {
			$val = bcadd($val, bcmul($arr[$str[$i]], bcpow($base, $len - $i - 1)));
		}

		return $val;
	}
}