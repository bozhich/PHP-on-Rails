<?php
// Link
function l($address, array $options = array()) {
	return Core_Url::getInstance()->getAddress($address, $options);
}

// Redirect
function r($address, $code = null, array $options = array()) {
	return Core_Url::getInstance()->redirect($address, $code, $options);
}

// Redirect Referer
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
function __($tag) {
	return $tag;

	return call_user_func(array('Cms_Translate', 'get'), $tag, array_slice(func_get_args(), 1));
}


// Page 404
function p404($reason = null, $type = null) {
	Core_Application::p404($reason, $type);
}

/**
 * @return Core_Cfg
 */
function cfg() {
	return Core_Cfg::getInstance();
}

function dd() {
	if (PHP_SAPI == 'cli') {
		$args = func_get_args();
		foreach ($args as $var) {
			var_dump($var);
		}
		die;
	}
	$has_bar = true;
	try {
		Core_Debug::getInstance()->getBar()->getCollector('DUMP');
	} catch (Exception $e) {
		$has_bar = false;
	}
	if (!$has_bar) {
		Core_Debug::getInstance()->getBar()->addCollector(new DebugBar\DataCollector\MessagesCollector('DUMP'));
	}
	$args = func_get_args();
	foreach ($args as $var) {
		Core_Debug::getInstance()->getBar()['DUMP']->info($var);
	}
}


function d() {
	if (PHP_SAPI == 'cli') {
		$args = func_get_args();
		foreach ($args as $var) {
			var_dump($var);
		}
	}
	$has_bar = true;
	try {
		Core_Debug::getInstance()->getBar()->getCollector('DUMP');
	} catch (Exception $e) {
		$has_bar = false;
	}
	if (!$has_bar) {
		Core_Debug::getInstance()->getBar()->addCollector(new DebugBar\DataCollector\MessagesCollector('DUMP'));
	}
	$args = func_get_args();
	foreach ($args as $var) {
		Core_Debug::getInstance()->getBar()['DUMP']->info($var);
	}
}

if (!function_exists('boolval')) {
	function boolval($val) {
		return (bool) $val;
	}
}
