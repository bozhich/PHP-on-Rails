<?php

/**
 * Class Core_Curl
 */
class Core_Curl extends Core_Singleton {
	const USER_AGENT = 'Mozilla/5.0 (X11; Linux x86_64; rv:37.0) Gecko/20100101 Firefox/37.0';
	const API_MODULE_NAME = 'api';
	private $_url;

	private $_cookies = array();
	private $_headers = array();
	private $_options = array();

	private $_multi_parent = false;
	private $_multi_child = false;
	private $_before_send = null;
	private $_success = null;
	private $_error = null;
	private $_complete = null;

	public $curl;
	public $curls;

	public $error = false;
	public $error_code = 0;
	public $error_message = null;

	public $curl_error = false;
	public $curl_error_code = 0;
	public $curl_error_message = null;

	public $http_error = false;
	public $http_status_code = 0;
	public $http_error_message = null;

	public $request_headers = null;
	public $response_headers = null;
	public $response = null;

	/**
	 * @throws ErrorException
	 */
	public function __construct() {
		if (!extension_loaded('curl')) {
			throw new \ErrorException('cURL library is not loaded');
		}

		$this->curl = curl_init();
		$this->setUserAgent(self::USER_AGENT);
		$this->setOpt(CURLINFO_HEADER_OUT, true);
		$this->setOpt(CURLOPT_HEADER, true);
		$this->setOpt(CURLOPT_RETURNTRANSFER, true);
	}


	/**
	 * @return mixed
	 */
	public function getUrl() {
		return $this->_url;
	}

	/**
	 * @param mixed $url
	 */
	public function setUrl($url) {
		$this->_url = $url;
	}

	/**
	 * @param       $url_mixed
	 * @param array $data
	 * @return int|mixed
	 * @throws ErrorException
	 */
	public function get($data = array()) {
		$this->setopt(CURLOPT_URL, $this->_buildURL($this->getUrl(), $data));
		$this->setopt(CURLOPT_HTTPGET, true);

		return $this->exec();
	}


	/**
	 * @param       $url_mixed
	 * @param array $data
	 * @return int|mixed
	 * @throws ErrorException
	 */
	public function send($data = array(), $params = array()) {
		@list($controller, $action, $id) = $data;

		$url = $this->getUrl() . '/' . self::API_MODULE_NAME . '/' . $controller . '/' . $action . '/' . $id;
		bd($url);
		$this->setopt(CURLOPT_URL, $url);
		if (!empty($params)) {
			bd($params);
			$this->setOpt(CURLOPT_POSTFIELDS, $this->_postfields($params));
			$this->setHeader('Expect:', '');
		} else {
			$this->setopt(CURLOPT_HTTPGET, true);
		}
		$result_code = $this->exec();
		if ($result_code != 0) {
			return $result_code;
		}

		return $this->response;
	}

	/**
	 * @param       $url
	 * @param array $data
	 * @return int|mixed
	 */
	public function post($data = array()) {
		die('not ready for usage');
		$this->setOpt(CURLOPT_URL, $this->_buildURL($this->getUrl()));
		$this->setOpt(CURLOPT_POSTFIELDS, $this->_postfields($data));

		return $this->exec();
	}

	/**
	 * @param       $url
	 * @param array $data
	 * @return int|mixed
	 */
	public function put($url, $data = array()) {
		die('not ready for usage');
		$this->setOpt(CURLOPT_URL, $url);
		$this->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
		$this->setOpt(CURLOPT_POSTFIELDS, http_build_query($data));

		return $this->exec();
	}

	/**
	 * @param       $url
	 * @param array $data
	 * @return int|mixed
	 */
	public function patch($url, $data = array()) {
		die('not ready for usage');
		$this->setOpt(CURLOPT_URL, $this->_buildURL($url));
		$this->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
		$this->setOpt(CURLOPT_POSTFIELDS, $data);

		return $this->exec();
	}

	/**
	 * @param       $url
	 * @param array $data
	 * @return int|mixed
	 */
	public function delete($url, $data = array()) {
		die('not ready for usage');
		$this->setOpt(CURLOPT_URL, $this->_buildURL($url, $data));
		$this->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');

		return $this->exec();
	}

	/**
	 * @param $username
	 * @param $password
	 */
	public function setBasicAuthentication($username, $password) {
		$this->setOpt(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		$this->setOpt(CURLOPT_USERPWD, $username . ':' . $password);
	}

	/**
	 * @param $key
	 * @param $value
	 */
	public function setHeader($key, $value) {
		$this->_headers[$key] = $key . ': ' . $value;
		$this->setOpt(CURLOPT_HTTPHEADER, array_values($this->_headers));
	}

	/**
	 * @param $user_agent
	 */
	public function setUserAgent($user_agent) {
		$this->setOpt(CURLOPT_USERAGENT, $user_agent);
	}

	/**
	 * @param $referrer
	 */
	public function setReferrer($referrer) {
		$this->setOpt(CURLOPT_REFERER, $referrer);
	}

	/**
	 * @param $key
	 * @param $value
	 */
	public function setCookie($key, $value) {
		$this->_cookies[$key] = $value;
		$this->setOpt(CURLOPT_COOKIE, http_build_query($this->_cookies, '', '; '));
	}

	/**
	 * @param $cookie_file
	 */
	public function setCookieFile($cookie_file) {
		$this->setOpt(CURLOPT_COOKIEFILE, $cookie_file);
	}

	/**
	 * @param $cookie_jar
	 */
	public function setCookieJar($cookie_jar) {
		$this->setOpt(CURLOPT_COOKIEJAR, $cookie_jar);
	}

	/**
	 * @param      $option
	 * @param      $value
	 * @param null $_ch
	 * @return bool
	 */
	public function setOpt($option, $value, $_ch = null) {
		$ch = is_null($_ch) ? $this->curl : $_ch;

		$required_options = array(
			CURLINFO_HEADER_OUT    => 'CURLINFO_HEADER_OUT',
			CURLOPT_HEADER         => 'CURLOPT_HEADER',
			CURLOPT_RETURNTRANSFER => 'CURLOPT_RETURNTRANSFER',
		);

		if (in_array($option, array_keys($required_options), true) && !($value === true)) {
			trigger_error($required_options[$option] . ' is a required option', E_USER_WARNING);
		}

		$this->_options[$option] = $value;

		return curl_setopt($ch, $option, $value);
	}

	/**
	 * @param bool $on
	 */
	public function verbose($on = true) {
		$this->setOpt(CURLOPT_VERBOSE, $on);
	}

	/**
	 *
	 */
	public function close() {
		if ($this->_multi_parent) {
			foreach ($this->curls as $curl) {
				curl_close($curl->curl);
			}
		}

		if (is_resource($this->curl)) {
			curl_close($this->curl);
		}
	}

	/**
	 * @param $function
	 */
	public function beforeSend($function) {
		$this->_before_send = $function;
	}

	/**
	 * @param $callback
	 */
	public function success($callback) {
		$this->_success = $callback;
	}

	/**
	 * @param $callback
	 */
	public function error($callback) {
		$this->_error = $callback;
	}

	/**
	 * @param $callback
	 */
	public function complete($callback) {
		$this->_complete = $callback;
	}

	/**
	 * @param       $url
	 * @param array $data
	 * @return string
	 */
	private function _buildURL($url, $data = array()) {
		return $url . (empty($data) ? '' : '?' . http_build_query($data));
	}

	/**
	 * @param $data
	 * @return array|string
	 */
	private function _postfields($data) {
		if (is_array($data)) {
			if (Core_Tools::is_array_multidim($data)) {
				$data = Core_Tools::http_build_multi_query($data);
			} else {
				// Fix "Notice: Array to string conversion" when $value in
				// curl_setopt($ch, CURLOPT_POSTFIELDS, $value) is an array
				// that contains an empty array.
				foreach ($data as $key => $value) {
					if (is_array($value) && empty($value)) {
						$data[$key] = '';
					}
				}
			}
		}

		return $data;
	}

	/**
	 * @param null $_ch
	 * @return int|m ixed
	 */
	protected function exec($_ch = null) {
		$ch = is_null($_ch) ? $this : $_ch;

		if ($ch->_multi_child) {
			$ch->response = curl_multi_getcontent($ch->curl);
		} else {
			$ch->response = curl_exec($ch->curl);
		}
		$ch->curl_error_code = curl_errno($ch->curl);
		$ch->curl_error_message = curl_error($ch->curl);
		$ch->curl_error = !($ch->curl_error_code === 0);
		$ch->http_status_code = curl_getinfo($ch->curl, CURLINFO_HTTP_CODE);
		$ch->http_error = in_array(floor($ch->http_status_code / 100), array(4, 5));
		$ch->error = $ch->curl_error || $ch->http_error;
		$ch->error_code = $ch->error ? ($ch->curl_error ? $ch->curl_error_code : $ch->http_status_code) : 0;


		$ch->request_headers = preg_split('/\r\n/', curl_getinfo($ch->curl, CURLINFO_HEADER_OUT), null, PREG_SPLIT_NO_EMPTY);
		$ch->response_headers = '';
		$this->response = $ch->response;
		if (!(strpos($ch->response, "\r\n\r\n") === false)) {
			list($response_header, $ch->response) = explode("\r\n\r\n", $ch->response, 2);
			if ($response_header === 'HTTP/1.1 100 Continue') {
				list($response_header, $ch->response) = explode("\r\n\r\n", $ch->response, 2);
			}
			$ch->response_headers = preg_split('/\r\n/', $response_header, null, PREG_SPLIT_NO_EMPTY);
		}

		$ch->http_error_message = $ch->error ? (isset($ch->response_headers['0']) ? $ch->response_headers['0'] : '') : '';
		$ch->error_message = $ch->curl_error ? $ch->curl_error_message : $ch->http_error_message;

		if (!$ch->error) {
			$ch->_call($this->_success, $ch);
		} else {
			$ch->_call($this->_error, $ch);
		}
		$ch->_call($this->_complete, $ch);

		return $ch->error_code;
	}

	/**
	 * @param $function
	 */
	private function _call($function) {
		if (is_callable($function)) {
			$args = func_get_args();
			array_shift($args);
			call_user_func_array($function, $args);
		}
	}

	/**
	 *
	 */
	public function __destruct() {
		$this->close();
	}
}
