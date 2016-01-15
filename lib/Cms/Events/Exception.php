<?php

/**
 * Class Events_Exception
 */
class Cms_Events_Exception extends Exception {
	/**
	 *
	 */
	const EXECUTION_FALED = 'General Fail';
	/**
	 *
	 */
	const EXECUTION_FALED_SQL_ERROR = 'Invalid SQL';


	/**
	 *
	 */
	const EXCEPTION_CODE_RETRY = 100;
	/**
	 *
	 */
	const EXCEPTION_CODE_BREAK = 200;
	/**
	 *
	 */
	const EXCEPTION_CODE_INVALID_SQL = 300;


	/**
	 * @param string $message
	 * @param int    $code
	 */
	public function __construct($message, $code = 0) {
		parent::__construct($message, $code);
	}

}