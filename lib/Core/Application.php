<?php
include(dirname(__FILE__) . DS . 'Singleton.php');

class Core_Application extends Core_Singleton {
	/**
	 * @var Core_Request
	 */
	protected $request;

	protected $dispatcher_is_breaked = false;


	public function __construct() {
		parent::__construct();
		// Register autoloader
		spl_autoload_register(array($this, 'autoloader'));

		// Get object aliases
		require_once LIB_PATH . 'aliases.php';

		// Set application properties
		$this->request = Core_Request::getInstance();
		// Load bootstrap
		$bootstrap_class = $this->request->getRoute('module') . '_BootstrapHelper';
		$bootstrap_class = strtoupper(substr($bootstrap_class, 0, 1)) . substr($bootstrap_class, 1);
		if (class_exists($bootstrap_class)) {
			new $bootstrap_class();
		}

		// Error logging
		$this->setErrorLogging();

		// Init view
		$view = Core_View::getInstance();
		$view->setLayoutFile('$layout/layout.phtml');

		// Dispatch
		$this->dispatch();

		// Show layout
		$view->displayLayout();
	}


	protected function setErrorLogging() {
		if (isset($_GET['debug_asdf']) || cfg()->dev_mode) {
			ini_set('display_errors', true);
		} else {
			ini_set('display_errors', false);
		}

		ini_set('ignore_repeated_errors', 'On');
		ini_set('log_errors', 'On');

		set_error_handler(array('Core_ErrorLog', 'errorHandler'));
		set_exception_handler(array('Core_ErrorLog', 'exceptionHandler'));
	}


	protected function autoloader($class_name) {
		$module_components = array(
			'controllers' => 'Controller',
			'models'      => 'Model',
			'helpers'     => 'Helper',
			'files'       => 'File', // mainly used for migrations
		);

		foreach ($module_components as $component_directory => $component_id) {
			// Ako zapochva s Core_ znachi niama da stoi v modulnite komponenti
			if (substr($class_name, 0, 5) == 'Core_') {
				break;
			}

			if (substr($class_name, -strlen($component_id)) == $component_id) {
				list($module) = explode('_', $class_name);
				$class_name = substr($class_name, strlen($module) + 1);
				$class_name = substr($class_name, 0, -strlen($component_id));
				$file = MODULES_PATH . strtolower($module) . DS . $component_directory . DS . str_replace('_', DS, $class_name) . '.php';
				break;
			}
		}

		require ROOT_PATH . 'public' . DS . 'vendor' . DS . 'autoload.php';


		if (empty($file)) {
			$file = LIB_PATH . str_replace('_', DS, $class_name) . '.php';
		}
		if (is_file($file)) {
			include_once $file;
		}
	}


	protected function dispatch() {
		if ($this->dispatcher_is_breaked) {
			return;
		}

		$module = $this->request->getRoute('module');

		$controller = $this->request->getRoute('controller');
		$controller = Core_String::toClass($module) . '_' . Core_String::toClass($controller) . 'Controller';

		if (class_exists($controller, true)) {
			$controller_instance = new $controller();

			if (method_exists($controller_instance, 'init')) {
				$controller_instance->init();
			}

			if ($this->dispatcher_is_breaked) {
				return;
			}

			$action = $this->request->getRoute('action');
			$action = Core_String::toFunction($action) . 'Action';

			if (method_exists($controller_instance, $action)) {
				$controller_instance->$action();

				return;
			}
		}

		p404('Missing: ' . $controller . (!empty($action) ? '::' . $action : null));
	}


	public function breakDispacher() {
		$this->dispatcher_is_breaked = true;
	}


	public static function p404($reason = null, $type = null) {
		header('HTTP/1.0 404 Not Found');

		$view = Core_View::getInstance();
		if (is_file($view->getDirectory() . '$maintenance/404.phtml')) {
			$view->reason = $reason;
			$view->type = $type;
			$view->backtrace = debug_backtrace();

			$view->setLayoutFile('$maintenance/404.phtml');
			$view->enableLayout();
			$view->displayLayout();
		} else {
			print '<h1>Page Not Found!</h1>';

			if ($reason) {
				print '<pre>' . $reason . '</pre>';
			}

			print '<pre>';
			debug_print_backtrace();
			print '</pre>';
		}

		die();
	}
}