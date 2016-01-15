<?php

abstract class Const_Abstract {

	public static function getTypes() {
		$reflection = new ReflectionClass(get_called_class());
		return $reflection->getConstants();
	}
}
