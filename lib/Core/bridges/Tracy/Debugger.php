<?php

/**
 * This file is part of the Tracy (http://tracy.nette.org)
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 */

namespace Tracy;

use ErrorException;
use Tracy;


/**
 * Debugger: displays and logs errors.
 *
 * @author     David Grudl
 */
class Debugger {
	const VERSION = '2.3-dev';

	/** server modes {@link Debugger::enable()} */
	const DEVELOPMENT = false,
		PRODUCTION = true,
		DETECT = null;

	const COOKIE_SECRET = 'tracy-debug';

	/** @deprecated */
	public static $version = self::VERSION;

	/** @var bool in production mode is suppressed any debugging output */
	public static $productionMode = self::DETECT;

	/** @var bool {@link Debugger::enable()} */
	private static $enabled = false;

	/** @var bool prevent double rendering */
	private static $done;

	/********************* errors and exceptions reporting ****************d*g**/

	/** @var bool|int determines whether any error will cause immediate death; if integer that it's matched against error severity */
	public static $strictMode = false;

	/** @var bool disables the @ (shut-up) operator so that notices and warnings are no longer hidden */
	public static $scream = false;

	/** @var array of callables specifies the functions that are automatically called after fatal error */
	public static $onFatalError = array();

	/********************* Debugger::dump() ****************d*g**/

	/** @var int  how many nested levels of array/object properties display {@link Debugger::dump()} */
	public static $maxDepth = 3;

	/** @var int  how long strings display {@link Debugger::dump()} */
	public static $maxLen = 150;

	/** @var bool display location? {@link Debugger::dump()} */
	public static $showLocation = false;

	/********************* logging ****************d*g**/

	/** @var string name of the directory where errors should be logged */
	public static $logDirectory;

	/** @var int  log bluescreen in production mode for this error severity */
	public static $logSeverity = 0;

	/** @var string|array email(s) to which send error notifications */
	public static $email;

	/** {@link Debugger::log()} and {@link Debugger::fireLog()} */
	const DEBUG = ILogger::DEBUG,
		INFO = ILogger::INFO,
		WARNING = ILogger::WARNING,
		ERROR = ILogger::ERROR,
		EXCEPTION = ILogger::EXCEPTION,
		CRITICAL = ILogger::CRITICAL;

	/********************* misc ****************d*g**/

	/** @var int timestamp with microseconds of the start of the request */
	public static $time;

	/** @deprecated */
	public static $source;

	/** @var string URI pattern mask to open editor */
	public static $editor = 'editor://open/?file=%file&line=%line';

	/** @var string command to open browser (use 'start ""' in Windows) */
	public static $browser;

	/** @var string custom static error template */
	public static $errorTemplate;

	/********************* services ****************d*g**/

	/** @var BlueScreen */
	private static $blueScreen;

	/** @var Bar */
	private static $bar;

	/** @var ILogger */
	private static $logger;

	/** @var ILogger */
	private static $fireLogger;


	/**
	 * Static class - cannot be instantiated.
	 */
	final public function __construct() {
		throw new \LogicException;
	}


	/**
	 * Enables displaying or logging errors and exceptions.
	 * @param  mixed   production, development mode, autodetection or IP address(es) whitelist.
	 * @param  string  error log directory; enables logging in production mode, FALSE means that logging is disabled
	 * @param  string  administrator email; enables email sending in production mode
	 * @return void
	 */
	public static function enable($mode = null, $logDirectory = null, $email = null) {
		self::$time = isset($_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : microtime(true);
		error_reporting(E_ALL | E_STRICT);

		if ($mode !== null || self::$productionMode === null) {
			self::$productionMode = is_bool($mode) ? $mode : !self::detectDebugMode($mode);
		}

		// logging configuration
		if ($email !== null) {
			self::$email = $email;
		}
		if ($logDirectory !== null) {
			self::$logDirectory = $logDirectory;
		}
		if (self::$logDirectory) {
			if (!is_dir(self::$logDirectory) || !preg_match('#([a-z]:)?[/\\\\]#Ai', self::$logDirectory)) {
				self::$logDirectory = null;
				self::exceptionHandler(new \RuntimeException('Logging directory not found or is not absolute path.'));
			}
			ini_set('error_log', self::$logDirectory . '/php_error.log');
		}

		// php configuration
		if (function_exists('ini_set')) {
			ini_set('display_errors', !self::$productionMode); // or 'stderr'
			ini_set('html_errors', false);
			ini_set('log_errors', false);

		} elseif (ini_get('display_errors') != !self::$productionMode // intentionally ==
			&& ini_get('display_errors') !== (self::$productionMode ? 'stderr' : 'stdout')
		) {
			self::exceptionHandler(new \RuntimeException("Unable to set 'display_errors' because function ini_set() is disabled."));
		}

		if (!self::$enabled) {
			register_shutdown_function(array(__CLASS__, 'shutdownHandler'));
			set_exception_handler(array(__CLASS__, 'exceptionHandler'));
			set_error_handler(array(__CLASS__, 'errorHandler'));

			array_map('class_exists', array('Tracy\Bar', 'Tracy\BlueScreen', 'Tracy\DefaultBarPanel', 'Tracy\Dumper',
				'Tracy\FireLogger', 'Tracy\Helpers', 'Tracy\Logger'));

			self::$enabled = true;
		}
	}


	/**
	 * @return bool
	 */
	public static function isEnabled() {
		return self::$enabled;
	}


	/**
	 * Shutdown handler to catch fatal errors and execute of the planned activities.
	 * @return void
	 * @internal
	 */
	public static function shutdownHandler() {
		if (self::$done) {
			return;
		}

		$error = error_get_last();
		if (in_array($error['type'], array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE, E_RECOVERABLE_ERROR, E_USER_ERROR), true)) {
			self::exceptionHandler(
				Helpers::fixStack(new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line'])),
				false
			);

		} elseif (!connection_aborted() && !self::$productionMode && self::isHtmlMode()) {
			self::getBar()->render();
		}
	}


	/**
	 * Handler to catch uncaught exception.
	 * @return void
	 * @internal
	 */
	public static function exceptionHandler(\Exception $exception, $exit = true) {
		if (self::$done) {
			return;
		}
		self::$done = true;

		if (!headers_sent()) {
			$protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
			$code = isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE ') !== false ? 503 : 500;
			header("$protocol $code", true, $code);
			if (self::isHtmlMode()) {
				header('Content-Type: text/html; charset=UTF-8');
			}
		}

		if (self::$productionMode) {
			try {
				self::log($exception, self::EXCEPTION);
			} catch (\Exception $e) {
			}

			if (self::isHtmlMode()) {
				$logged = empty($e);
				require self::$errorTemplate ? : __DIR__ . '/templates/error.phtml';
			} elseif (PHP_SAPI === 'cli') {
				fwrite(STDERR, 'ERROR: application encountered an error and can not continue. '
					. (isset($e) ? "Unable to log error.\n" : "Error was logged.\n"));
			}

		} elseif (!connection_aborted() && self::isHtmlMode()) {
			self::getBlueScreen()->render($exception);
			self::getBar()->render();

		} else {
			self::fireLog($exception);
			try {
				$file = self::log($exception, self::EXCEPTION);
				if ($file && !headers_sent()) {
					header("X-Tracy-Error-Log: $file");
				}
				echo "$exception\n" . ($file ? "(stored in $file)\n" : '');
				if ($file && self::$browser) {
					exec(self::$browser . ' ' . escapeshellarg($file));
				}
			} catch (\Exception $e) {
				echo "$exception\nUnable to log error: {$e->getMessage()}\n";
			}
		}

		try {
			foreach (self::$onFatalError as $handler) {
				call_user_func($handler, $exception);
			}
		} catch (\Exception $e) {
			try {
				self::log($e, self::EXCEPTION);
			} catch (\Exception $e) {
			}
		}

		if ($exit) {
			exit(254);
		}
	}


	/**
	 * Handler to catch warnings and notices.
	 * @return bool   FALSE to call normal error handler, NULL otherwise
	 * @throws ErrorException
	 * @internal
	 */
	public static function errorHandler($severity, $message, $file, $line, $context) {
		if (self::$scream) {
			error_reporting(E_ALL | E_STRICT);
		}

		if ($severity === E_RECOVERABLE_ERROR || $severity === E_USER_ERROR) {
			if (Helpers::findTrace(debug_backtrace(PHP_VERSION_ID >= 50306 ? DEBUG_BACKTRACE_IGNORE_ARGS : false), '*::__toString')) {
				$previous = isset($context['e']) && $context['e'] instanceof \Exception ? $context['e'] : null;
				$e = new ErrorException($message, 0, $severity, $file, $line, $previous);
				$e->context = $context;
				self::exceptionHandler($e);
			}

			$e = new ErrorException($message, 0, $severity, $file, $line);
			$e->context = $context;
			throw $e;

		} elseif (($severity & error_reporting()) !== $severity) {
			return false; // calls normal error handler to fill-in error_get_last()

		} elseif (self::$productionMode && ($severity & self::$logSeverity) === $severity) {
			$e = new ErrorException($message, 0, $severity, $file, $line);
			$e->context = $context;
			try {
				self::log($e, self::ERROR);
			} catch (\Exception $foo) {
			}

			return null;

		} elseif (!self::$productionMode && !isset($_GET['_tracy_skip_error'])
			&& (is_bool(self::$strictMode) ? self::$strictMode : ((self::$strictMode & $severity) === $severity))
		) {
			$e = new ErrorException($message, 0, $severity, $file, $line);
			$e->context = $context;
			$e->skippable = true;
			self::exceptionHandler($e);
		}

		$message = 'PHP ' . Helpers::errorTypeToString($severity) . ": $message";
		$count = &self::getBar()->getPanel(__CLASS__ . ':errors')->data["$file|$line|$message"];

		if ($count++) { // repeated error
			return null;

		} elseif (self::$productionMode) {
			try {
				self::log("$message in $file:$line", self::ERROR);
			} catch (\Exception $foo) {
			}

			return null;

		} else {
			self::fireLog(new ErrorException($message, 0, $severity, $file, $line));

			return self::isHtmlMode() ? null : false; // FALSE calls normal error handler
		}
	}


	private static function isHtmlMode() {
		return empty($_SERVER['HTTP_X_REQUESTED_WITH'])
		&& PHP_SAPI !== 'cli'
		&& !preg_match('#^Content-Type: (?!text/html)#im', implode("\n", headers_list()));
	}


	/********************* services ****************d*g**/


	/**
	 * @return BlueScreen
	 */
	public static function getBlueScreen() {
		if (!self::$blueScreen) {
			self::$blueScreen = new BlueScreen;
			self::$blueScreen->info = array(
				'PHP ' . PHP_VERSION,
				isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : null,
				'Tracy ' . self::VERSION,
			);
		}

		return self::$blueScreen;
	}


	/**
	 * @return Bar
	 */
	public static function getBar() {
		if (!self::$bar) {
			self::$bar = new Bar;
			self::$bar->addPanel(new DefaultBarPanel('time'));
			self::$bar->addPanel(new DefaultBarPanel('memory'));
			self::$bar->addPanel(new DefaultBarPanel('errors'), __CLASS__ . ':errors'); // filled by errorHandler()
			self::$bar->info = array(
				'PHP ' . PHP_VERSION,
				isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : null,
				'Tracy ' . self::VERSION,
			);
		}

		return self::$bar;
	}


	/**
	 * @return void
	 */
	public static function setLogger(ILogger $logger) {
		self::$logger = $logger;
	}


	/**
	 * @return ILogger
	 */
	public static function getLogger() {
		if (!self::$logger) {
			self::$logger = new Logger(self::$logDirectory, self::$email, self::getBlueScreen());
			self::$logger->directory = &self::$logDirectory; // back compatiblity
			self::$logger->email = &self::$email;
		}

		return self::$logger;
	}


	/**
	 * @return ILogger
	 */
	public static function getFireLogger() {
		if (!self::$fireLogger) {
			self::$fireLogger = new FireLogger;
		}

		return self::$fireLogger;
	}


	/********************* useful tools ****************d*g**/


	/**
	 * Dumps information about a variable in readable format.
	 * @tracySkipLocation
	 * @param  mixed  variable to dump
	 * @param  bool   return output instead of printing it? (bypasses $productionMode)
	 * @return mixed  variable itself or dump
	 */
	public static function dump($var, $return = false) {
		if ($return) {
			ob_start();
			Dumper::dump($var, array(
				Dumper::DEPTH    => self::$maxDepth,
				Dumper::TRUNCATE => self::$maxLen,
			));

			return ob_get_clean();

		} elseif (!self::$productionMode) {
			Dumper::dump($var, array(
				Dumper::DEPTH    => self::$maxDepth,
				Dumper::TRUNCATE => self::$maxLen,
				Dumper::LOCATION => self::$showLocation,
			));
		}

		return $var;
	}


	/**
	 * Starts/stops stopwatch.
	 * @param  string  name
	 * @return float   elapsed seconds
	 */
	public static function timer($name = null) {
		static $time = array();
		$now = microtime(true);
		$delta = isset($time[$name]) ? $now - $time[$name] : 0;
		$time[$name] = $now;

		return $delta;
	}


	/**
	 * Dumps information about a variable in Tracy Debug Bar.
	 * @tracySkipLocation
	 * @param  mixed  variable to dump
	 * @param  string optional title
	 * @param  array  dumper options
	 * @return mixed  variable itself
	 */
	public static function barDump($var, $title = null, array $options = null) {
		if (!self::$productionMode) {
			static $panel;
			if (!$panel) {
				self::getBar()->addPanel($panel = new DefaultBarPanel('dumps'));
			}
			$panel->data[] = array('title' => $title, 'dump' => Dumper::toHtml($var, (array) $options + array(
					Dumper::DEPTH    => self::$maxDepth,
					Dumper::TRUNCATE => self::$maxLen,
					Dumper::LOCATION => self::$showLocation,
				)));
		}

		return $var;
	}


	/**
	 * Logs message or exception.
	 * @param  string|Exception
	 * @return mixed
	 */
	public static function log($message, $priority = ILogger::INFO) {
		return self::getLogger()->log($message, $priority);
	}


	/**
	 * Sends message to FireLogger console.
	 * @param  mixed   message to log
	 * @return bool    was successful?
	 */
	public static function fireLog($message) {
		if (!self::$productionMode) {
			return self::getFireLogger()->log($message);
		}
	}


	/**
	 * Detects debug mode by IP address.
	 * @param  string|array IP addresses or computer names whitelist detection
	 * @return bool
	 */
	public static function detectDebugMode($list = null) {
		$addr = isset($_SERVER['REMOTE_ADDR'])
			? $_SERVER['REMOTE_ADDR']
			: php_uname('n');
		$secret = isset($_COOKIE[self::COOKIE_SECRET]) && is_string($_COOKIE[self::COOKIE_SECRET])
			? $_COOKIE[self::COOKIE_SECRET]
			: null;
		$list = is_string($list)
			? preg_split('#[,\s]+#', $list)
			: (array) $list;
		if (!isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$list[] = '127.0.0.1';
			$list[] = '::1';
		}

		return in_array($addr, $list, true) || in_array("$secret@$addr", $list, true);
	}

}
