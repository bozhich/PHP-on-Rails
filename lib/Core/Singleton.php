<?php

/**
 * Class Core_Singleton
 */
abstract class Core_Singleton {
	/**
	 * @var array
	 */
	private static $instances = array();


	/**
	 * @throws Exception
	 */
	protected function __construct() {
		if (isset(self::$instances[get_called_class()])) {
			throw new Exception('Class ' . get_called_class() . ' has already instanced');
		}
		self::$instances[get_called_class()] = $this;
	}


	/**
	 * @return mixed
	 */
	final public static function getInstance() {
		$called_class_name = get_called_class();

		if (!isset(self::$instances[$called_class_name])) {
			self::$instances[$called_class_name] = new $called_class_name();
		}

		return self::$instances[$called_class_name];
	}


	/**
	 * @return bool
	 */
	final public static function hasInstance() {
		$called_class_name = get_called_class();

		return isset(self::$instances[$called_class_name]);
	}


	/**
	 *
	 */
	final private function __clone() {
	}
}
