<?php

/**
 * Class Cms_Events_Type_Factory
 */
class Cms_Events_Type_Factory {
	/**
	 * @var array
	 */
	private static $objMap = array(
		Cms_Events_Type_Abstract::EVENT_TYPE_TEST                      => 'Cms_Events_Type_Test',
	);


	/**
	 * @param $type
	 * @return Cms_Events_Type_Abstract or false
	 */
	public static function getObject($type) {
		if (isset(self::$objMap[$type])) {
			return new self::$objMap[$type]();
		}

		return false;
	}


	public static function getEventsRequirements() {
		$requirements_list = array();
		/**
		 * @var  $type_id
		 * @var  $class_name Cms_Events_Type_Abstract
		 */
		foreach (self::$objMap as $type_id => $class_name) {
			$requirements_list[$type_id] = $class_name::getRequirements();
		}

		return $requirements_list;
	}
}

