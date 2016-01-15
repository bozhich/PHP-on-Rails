<?php

/**
 * Class Core_Bootstrap
 */
class Core_Bootstrap {
	/**
	 *
	 */
	final public function __construct() {
		foreach (get_class_methods($this) as $method) {
			if ($method == '__construct') {
				continue;
			}

			$this->$method();
		}
	}


	/**
	 * @return Core_Request
	 */
	final public function getRequest() {
		return Core_Request::getInstance();
	}

	final public function getView() {
		return Core_View::getInstance();
	}
}
