<?php
// Link
/**
 * @param       $address
 * @param array $options
 * @return mixed
 */
function l($address, array $options = array()) {
	return Core_Url::getInstance()->getAddress($address, $options);
}

// Redirect
/**
 * @param       $address
 * @param null  $code
 * @param array $options
 * @return mixed
 */
function r($address, $code = null, array $options = array()) {
	return Core_Url::getInstance()->redirect($address, $code, $options);
}

// Redirect Referer
/**
 * @param null $address
 */
function rr($address = null) {
	$request = Core_Request::getInstance();
	if ($request->getServer('HTTP_REFERER')) {
		if (strstr($request->getServer('HTTP_REFERER'), $request->getServer('REQUEST_URI'))) {
			r('/');
		} else {
			r($request->getServer('HTTP_REFERER'), null, array(
				Core_Url::DIRECT_REDIRECT => true,
			));
		}
	} else {
		r($address);
	}
}

/**
 * @return Core_Session
 */
function s() {
	return Core_Session::getInstance();
}

// Translate
/**
 * @param $tag
 * @return mixed
 */
function __($tag) {
	return $tag;

	return call_user_func(array('Cms_Translate', 'get'), $tag, array_slice(func_get_args(), 1));
}


// Page 404
/**
 * @param null $reason
 * @param null $type
 */
function p404($reason = null, $type = null) {
	Core_Application::p404($reason, $type);
}

/**
 * @return Core_Cfg
 */
function cfg() {
	return Core_Cfg::getInstance();
}

if (!function_exists('boolval')) {
	/**
	 * @param $val
	 * @return bool
	 */
	function boolval($val) {
		return (bool) $val;
	}
}
