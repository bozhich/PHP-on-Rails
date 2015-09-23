<?php

/**
 * Class Core_Request
 */
class Core_Request extends Core_Singleton {
	/**
	 * @var array
	 */
	protected $store = array();


	/**
	 * @param $var
	 * @return null
	 */
	public function getPost($var) {
		return (array_key_exists($var, $_POST)) ? $_POST[$var] : null;
	}


	/**
	 * @return mixed
	 */
	public function getAllPosts() {
		return $_POST;
	}


	/**
	 * @param $var
	 * @return null
	 */
	public function getQuery($var) {
		return (array_key_exists($var, $_GET)) ? $_GET[$var] : null;
	}


	/**
	 * @return mixed
	 */
	public function getAllQueries() {
		return $_GET;
	}


	/**
	 * @param $var
	 * @return null
	 */
	public function getCookie($var) {
		return (array_key_exists($var, $_COOKIE)) ? $_COOKIE[$var] : null;
	}


	/**
	 * @return mixed
	 */
	public function getAllCookies() {
		return $_COOKIE;
	}


	/**
	 * @param $route_id
	 * @return mixed
	 */
	public function getRoute($route_id) {
		return Core_Request_Routes::getInstance()->{$route_id};
	}


	/**
	 * @return mixed
	 */
	public function getAllRoutes() {
		return Core_Request_Routes::getInstance()->getRoutes();
	}


	/**
	 * @param $var
	 * @return null
	 */
	public function getServer($var) {
		return (array_key_exists($var, $_SERVER)) ? $_SERVER[$var] : null;
	}


	/**
	 * @return mixed
	 */
	public function getAllServers() {
		return $_SERVER;
	}


	/**
	 * @param $key
	 * @return null
	 */
	public function getArgv($key) {
		return isset($_SERVER['argv'][$key]) ? $_SERVER['argv'][$key] : null;
	}


	/**
	 * @param $file_id
	 * @return null
	 */
	public function getFile($file_id) {
		if (isset($_FILES[$file_id]) && is_file($_FILES[$file_id]['tmp_name'])) {
			$file = $_FILES[$file_id];
			$file['extension'] = strtolower(substr(strrchr($file['name'], '.'), 1));
		} else {
			$file = null;
		}

		return $file;
	}


	/**
	 * @param $var
	 * @return mixed|null
	 */
	public function getParam($var) {
		if (isset($this->store[$var])) {
			return $this->store[$var];
		}

		$param = $this->getPost($var);
		if (isset($param)) {
			return $param;
		}

		$param = $this->getQuery($var);
		if (isset($param)) {
			return $param;
		}

		$param = $this->getCookie($var);
		if (isset($param)) {
			return $param;
		}

		$param = $this->getRoute($var);
		if (isset($param)) {
			return $param;
		}

		return null;
	}


	/**
	 * @return string
	 */
	public function getHomeDir() {
		$url = parse_url(cfg()->game_address);

		return !empty($url['path']) ? $url['path'] : '/';
	}


	/**
	 * @param $var
	 * @return mixed|null
	 */
	public function __get($var) {
		return $this->getParam($var);
	}


	/**
	 * @param $var
	 * @return bool
	 */
	public function __isset($var) {
		return $this->getParam($var) !== null;
	}


	/**
	 * @return bool
	 */
	public function isAjax() {
		return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}
}