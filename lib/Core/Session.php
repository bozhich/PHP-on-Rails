<?php

/**
 * Class Core_Session
 */
class Core_Session extends Core_Singleton {
	/**
	 *
	 */
	const NAME = 'XSS';

	/**
	 *
	 */
	const NAME_PERSISTENT = 'XSS_persistent';

	/**
	 *
	 */
	const PERSISTENT_TIME = 2592000; // 30 days

	/**
	 * @var
	 */
	protected $request;

	/**
	 * @var array
	 */
	protected $registries = array();


	/**
	 * @throws Exception
	 */
	public function __construct() {
		parent::__construct();

//		if (PHP_SAPI == 'cli') {
//			throw new Exception('You can`t create session in console');
//		}

		$this->request = Core_Request::getInstance();

		// Start session
		$session_id = $this->request->{self::NAME};

		if ($session_id) {
			session_id($session_id);
		}

		if ($this->request->getCookie(self::NAME_PERSISTENT)) {
			session_set_cookie_params(self::PERSISTENT_TIME, $this->request->getHomeDir());
		} else {
			session_set_cookie_params(0, $this->request->getHomeDir());
		}

		session_name(self::NAME);

		// Try session start many times
		for ($i = 1; $i <= 5; $i++) {
			if (session_start()) {
				break;
			}

			usleep(100000);
		}
	}


	/**
	 * @param $namespace
	 * @return mixed
	 */
	public function __get($namespace) {
		if (!isset($this->registries[$namespace])) {
			$this->registries[$namespace] = new Core_Session_Registry($this, $namespace);
		}

		return $this->registries[$namespace];
	}

	public function __isset($namespace) {
		return isset($this->registries[$namespace]);
	}


	/**
	 * @param $var
	 * @param $value
	 * @throws Exception
	 */
	public function __set($var, $value) {
		throw new Exception('You can`t set namespace values');
	}


	/**
	 * @param $namespace
	 */
	public function __unset($namespace) {
		if (isset($_SESSION[$namespace])) {
			unset($this->registries[$namespace]);
			unset($_SESSION[$namespace]);
		}
	}


	/**
	 * @return mixed
	 */
	public function getAll() {
		return $_SESSION;
	}


	/**
	 * @return string
	 */
	public function getId() {
		return session_id();
	}


	/**
	 *
	 */
	public function setPersistent() {
		$token = $this->registries['user']->id . '#' . time();
		$token = Core_Crypt::generete($token, self::NAME_PERSISTENT);
		setcookie(self::NAME_PERSISTENT, $token, time() + self::PERSISTENT_TIME, $this->request->getHomeDir());

		Default_PlayersModel::set(array(
			'token' => $token,
		), array(
			'id' => $this->registries['user']->id,
		));
	}


	/**
	 *
	 */
	public function flush() {
		$_SESSION = array();
	}


	/**
	 *
	 */
	public function destroy() {
		session_destroy();
		setcookie(self::NAME, null, -1, $this->request->getHomeDir());
		setcookie(self::NAME_PERSISTENT, 1, time() - self::PERSISTENT_TIME, $this->request->getHomeDir());
	}


	/**
	 * @param array $data
	 */
	public function merge(array $data) {
		$_SESSION = array_merge($_SESSION, $data);
	}


	/**
	 * @param $data
	 */
	public function set($data) {
		if (!isset($_SESSION['misc'])) {
			$_SESSION['misc'] = array();
		}
		$_SESSION['misc'] = array_merge($_SESSION['misc'], $data);
	}


	/**
	 * @param array $data
	 */
	public function setAll(array $data) {
		$_SESSION = $data;
	}
}