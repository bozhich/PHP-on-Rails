<?php

/**
 * Class Core_Session_Registry
 */
class Core_Session_Registry {
	protected $namespace;


	/**
	 * @param Core_Session $session
	 * @param              $namespace
	 */
	public function __construct(Core_Session $session, $namespace) {
		$this->namespace = $namespace;
	}


	/**
	 * @param $var
	 * @param $value
	 */
	public function __set($var, $value) {
		$_SESSION[$this->namespace][$var] = $value;
	}


	/**
	 * @param $var
	 * @return null
	 */
	public function __get($var) {
		if (!isset($_SESSION[$this->namespace])) {
			return null;
		}

		return (array_key_exists($var, $_SESSION[$this->namespace])) ? $_SESSION[$this->namespace][$var] : null;
	}


	/**
	 * @param $var
	 * @return bool|null
	 */
	public function __isset($var) {
		if (!isset($_SESSION[$this->namespace])) {
			return null;
		}

		return isset($_SESSION[$this->namespace][$var]);
	}


	/**
	 * @param $var
	 */
	public function __unset($var) {
		if (isset($_SESSION[$this->namespace][$var])) {
			unset($_SESSION[$this->namespace][$var]);
		}
	}


	/**
	 * @return mixed
	 */
	public function getAll() {
		if (isset($_SESSION[$this->namespace])) {
			return $_SESSION[$this->namespace];
		}
	}
}
