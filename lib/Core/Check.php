<?php

/**
 * Class Core_Check
 */
class Core_Check {
	/**
	 * @param $email
	 * @return int
	 */
	public static function email($email) {
		// http://fightingforalostcause.net/misc/2006/compare-email-regex.php
		return preg_match("/^[-_a-z0-9\'+*$^&%=~!?{}]++(?:\.[-_a-z0-9\'+*$^&%=~!?{}]+)*+@(?:(?![-.])[-a-z0-9.]+(?<![-.])\.[a-z]{2,6}|\d{1,3}(?:\.\d{1,3}){3})(?::\d++)?$/iD", $email);
	}

	/**
	 * @param $user
	 * @return int
	 */
	public static function user($user) {
		//return preg_match("/[a-zA-Zа-яА-Я0-9._-]$/", $user);
		return preg_match("/[a-zA-Z0-9._-]$/", $user);
	}

	/**
	 * @param $password
	 * @return bool
	 */
	public static function password($password) {
		return (strlen($password) >= cfg()->min_pass_lenght);
	}
}
