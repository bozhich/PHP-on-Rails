<?php

/**
 * Class Core_Bit
 */
class Core_Bit {
	/**
	 * @param $flag
	 * @param $bit
	 * @return bool
	 */
	public static function check($flag, $bit) {
		return ($flag & $bit) == $bit;
	}


	/**
	 * @param $flag
	 * @param $bit
	 * @return mixed
	 */
	public static function add($flag, $bit) {
		if (!self::check($flag, $bit)) {
			$flag ^= $bit;
		}

		return $flag;
	}


	/**
	 * @param $flag
	 * @param $bit
	 * @return mixed
	 */
	public static function remove($flag, $bit) {
		if (self::check($flag, $bit)) {
			$flag ^= $bit;
		}

		return $flag;
	}


	/**
	 * @param $flag
	 * @param $bit
	 * @return mixed
	 */
	public static function toggle($flag, $bit) {
		$flag ^= $bit;

		return $flag;
	}
}
