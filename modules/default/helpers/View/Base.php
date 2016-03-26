<?php

/**
 * Class Default_View_BaseHelper
 */
class Default_View_BaseHelper {
	/**
	 * @return null
	 */
	public function getStaticAddress() {
		return cfg()->static_address;
	}

	/**
	 * @return mixed
	 */
	public function getRequest() {
		return Core_Request::getInstance();
	}
}