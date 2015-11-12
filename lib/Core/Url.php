<?php

/**
 * Class Core_Url
 */
class Core_Url extends Core_Singleton {
	/**
	 * @var Core_Request
	 */
	protected $request;

	const STRICT_LINK = 1; // bool
	const ADD_CURRENT_QUERY = 2; // bool
	const ADD_QUERY = 3; // array
	const SET_MODULE = 4; // string
	const DIRECT_REDIRECT = 5; // bool


	/**
	 *
	 */
	public function __construct() {
		$this->request = Core_Request::getInstance();
	}


	/**
	 * @param       $address
	 * @param bool  $lang
	 * @param array $options
	 * @return mixed|string
	 */
	public function getAddress($address, array $options = array()) {
		if (substr($address, 0, 7) == 'http://' || substr($address, 0, 8) == 'https://') {
			return $address;
		}

		if (!isset($address)) {
			$tmp_parts = Core_Request_Routes::getInstance()->getRoutes();
			unset($tmp_parts['module']);
			$address = implode('/', $tmp_parts);
			unset($tmp_parts);
		}

		// Create query
		$query = array();
		if (!empty($options[self::ADD_CURRENT_QUERY])) {
			$query = array_merge($query, $this->request->getAllQueries());
		}

		if (strstr($address, '?')) {
			parse_str(preg_replace('[.*\?]', null, $address), $query_tmp);
			$query = array_merge($query, $query_tmp);

			$address = preg_replace('[\?.*]', null, $address);
		}

		if (isset($options[self::ADD_QUERY])) {
			$query = array_merge($query, $options[self::ADD_QUERY]);
		}

		// Create $address
		$module = isset($options[self::SET_MODULE]) ? $options[self::SET_MODULE] : $this->request->getRoute('module');
		if ($module == 'default') {
			$module = null;
		}


		$address = '/' . $module . '/' . $address;
		$address_parts = array();

		foreach (explode('/', $address) as $part) {
			if ($part) {
				if (substr($part, 0, 1) == ':') {
					$part = $this->request->getRoute(substr($part, 1));
				}
				// skip displaying index files
				if ($part == 'index') {
					continue;
				}
				$address_parts[] = $part;
			}
		}
		$address = cfg()->game_address . '/' . implode('/', $address_parts);

		// Prepare to return
		if ($query) {
			$address .= '&' . http_build_query($query);
		}

		if (!empty($options[self::STRICT_LINK])) {
			$address = str_replace('&', '&amp;', $address);
		}

		return $address;
	}


	/**
	 * @param       $address
	 * @param null  $code
	 * @param array $options
	 */
	public function redirect($address, $code = null, array $options = array()) {
		if (!isset($code)) {
			$code = 302;
		}

		if (empty($options[self::DIRECT_REDIRECT])) {
			$address = $this->getAddress($address, $options);
		}
		if ($this->request->isAjax()) {
			//json
			return Core_Response::getInstance()->redirect($address)->toJson();
		}


			header('Location: ' . $address, true, $code);
		die();
	}
}