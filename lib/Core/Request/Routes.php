<?php

/**
 * Class Core_Request_Routes
 */
class Core_Request_Routes extends Core_Singleton {
	const ROUTER_NAME = 'r';

	protected static $server_router;

	protected $default_scheme = array(
		1 => array('name' => 'controller', 'empty_value' => 'index'),
		2 => array('name' => 'action', 'empty_value' => 'index'),
		3 => array('name' => 'id'),
	);

	protected $routes = array();


	/**
	 * @throws Exception
	 */
	public function __construct() {
		parent::__construct();

		if (!empty($_GET[self::ROUTER_NAME])) {
			self::$server_router = trim($_GET[self::ROUTER_NAME], '/');
			unset($_GET[self::ROUTER_NAME]);
		} elseif (!empty($_SERVER['argv'])) {
			$tmp_parts = array();
			for ($i = 2; $i < count($_SERVER['argv']); $i++) {
				$tmp_parts[] = $_SERVER['argv'][$i];
			}
			self::$server_router = implode('/', $tmp_parts);
		}

		$this->setRoutes($this->getDefaultScheme());
	}


	/**
	 * @param $var
	 * @return null
	 */
	public function __get($var) {
		return (array_key_exists($var, $this->routes)) ? $this->routes[$var] : null;
	}


	/**
	 * @param $route_id
	 * @param $value
	 */
	public function __set($route_id, $value) {
		$this->routes[$route_id] = $value;
	}


	/**
	 * @param $scheme
	 */
	public function setRoutes($scheme) {
		if (empty($scheme)) {
			return;
		}

		// reset
		$this->routes = array();

		// set empty values
		foreach ($scheme as $row) {
			$this->routes[$row['name']] = isset($row['empty_value']) ? $row['empty_value'] : null;
		}

		// set url values
		$tmp_levels = explode('/', self::$server_router);

		if (!empty($tmp_levels[0]) && is_dir(MODULES_PATH . $tmp_levels[0] . DS)) {
			$this->routes['module'] = $tmp_levels[0];
			$start_level = 1;
		} else {
			$this->routes['module'] = 'default';
			$start_level = 0;
		}

		for ($i = $start_level, $s_id = 1; $i < count($tmp_levels); $i++, $s_id++) {
			if (empty($scheme[$s_id]['name'])) {
				break;
			}

			if (empty($tmp_levels[$i])) {
				continue;
			}

			$scheme_id = $scheme[$s_id]['name'];
			$this->routes[$scheme_id] = $tmp_levels[$i];
		}
	}


	/**
	 * @return array
	 */
	public function getRoutes() {
		return $this->routes;
	}


	/**
	 * @return array
	 */
	public function getDefaultScheme() {
		return $this->default_scheme;
	}
}
