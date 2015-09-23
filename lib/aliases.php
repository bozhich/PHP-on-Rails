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

// Session
function s() {
	return Core_Session::getInstance();
}

// Translate
function __($tag) {
	return call_user_func(array('Cms_Translate', 'get'), $tag, array_slice(func_get_args(), 1));
}


// Page 404
function p404($reason = null, $type = null) {
	Core_Application::p404($reason, $type);
}

// Cfg
function cfg() {
	return Core_Cfg::getInstance();
}

function df($file, $line) {
	echo Tracy\BlueScreen::highlightFile($file, $line);
}

function dd() {
	$args = func_get_args();
	foreach ($args as $var) {
		Tracy\Debugger::dump($var);
	}
	die();
}

function d() {
	$args = func_get_args();
	foreach ($args as $var) {
		Tracy\Debugger::dump($var);
	}
}

function bd() {
	$args = array_reverse(func_get_args());
	$title = '[dump]';
	foreach ($args as $var) {
		if (is_string($var)) {
			if (preg_match('#\[(.*)\]#', $var)) {
				$title = $var;
				continue;
			}
		}
		Tracy\Debugger::barDump($var, $title);
	}
}