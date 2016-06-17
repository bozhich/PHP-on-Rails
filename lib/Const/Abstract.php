<?php

/**
 * Class Const_Abstract
 */
abstract class Const_Abstract {

	/**
	 * @return array
	 */
	public static function getTypes() {
		$reflection = new ReflectionClass(get_called_class());
		return $reflection->getConstants();
	}
}
