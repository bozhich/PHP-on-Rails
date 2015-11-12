<?php

/**
 * Class Core_Controller
 */
abstract class Core_Controller {
	/**
	 *
	 */
	public final function __construct() {
	}


	/**
	 * @return Core_View
	 */
	public function getView() {
		return Core_View::getInstance();
	}


	/**
	 * @return Core_Request
	 */
	public function getRequest() {
		return Core_Request::getInstance();
	}

	/**
	 * @return Core_Response
	 */
	public function getResponse() {
		return Core_Response::getInstance();
	}

	/**
	 * @return Core_Curl
	 */
	public function getApi() {
		$curl = Core_Curl::getInstance();
		$curl->setUrl(s()->version->current->url);

		return $curl;
	}
}
