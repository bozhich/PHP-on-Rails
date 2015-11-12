<?php

/**
 * Class Core_View
 */
class Core_View extends Core_Singleton {
	protected $layout_file = null;

	protected $file_directory = null;

	protected $main_file = null;

	protected $disable_layout_file = false;

	protected $disable_main_file = false;

	protected $store = array();

	protected $helpers = array();

	protected $main_code = null;

	protected $request = null;

	protected $ajaxPage = false;

	protected $header_code = '';

	/**
	 * @throws Exception
	 */
	public function __construct() {
		parent::__construct();
		$this->request = Core_Request::getInstance();
	}


	/**
	 *
	 */
	protected function initPaths() {
		$this->file_directory = $this->getDirectory();

		if (!$this->main_file) {
			$main_file = $this->request->getRoute('controller');
			$main_file .= DS . $this->request->getRoute('action') . '.phtml';


			$this->setMainFile($main_file);
		}
	}


	/**
	 * @return string
	 */
	public function getDirectory() {
		return MODULES_PATH . $this->request->getRoute('module') . DS . 'views' . DS;
	}

	////

	/**
	 * @param $layout_file
	 */
	public function setLayoutFile($layout_file) {
		$this->layout_file = $layout_file;
	}


	/**
	 *
	 */
	public function displayLayout() {
		if ($this->ajaxPage) {
			$this->layout_file = '$layout/empty.phtml';
		}

		if (!$this->disable_layout_file) {
			$this->initPaths();

			print $this->fetch($this->layout_file);
			die;
		}
	}


	/**
	 *
	 */
	public function disableLayout() {
		$this->disable_layout_file = true;
	}


	/**
	 *
	 */
	public function enableLayout() {
		$this->disable_layout_file = false;
	}

	/**
	 * @return bool
	 */
	public function hasLayout() {
		return !$this->disable_layout_file;
	}

	/**
	 * @param $hepler
	 */
	public function addHelper($hepler) {
		$this->helpers[] = $hepler;
	}


	/**
	 * @param $code
	 */
	public function addMainCode($code) {
		$this->main_code .= $code;
	}


	/**
	 * @param $main_file
	 */
	public function setMainFile($main_file) {
		$this->main_file = $main_file;
	}


	/**
	 *
	 */
	protected function displayMain() {
		print $this->main_code;

		if (!$this->disable_main_file) {

			$this->_include($this->main_file);
		}
	}


	/**
	 *
	 */
	public function disableMain() {
		$this->disable_main_file = true;
	}


	/**
	 *
	 */
	public function enableMain() {
		$this->disable_main_file = false;
	}

	////

	/**
	 * @param $file
	 * @return string
	 */
	public function fetch($file = null) {
		if (is_null($this->file_directory)) {
			$this->initPaths();
		}
		if (!$file) {
			$file = $this->main_file;
		}

		// Start observing and get template
		ob_start();
		$this->_include($file);
		$contents = ob_get_contents();
		ob_end_clean();

		if (cfg()->dev_mode) {
			return $contents;
		}

		@list($head, $body) = explode('<body', $contents);
		$body = '<body ' . $body;

		// work on the head
		$head = preg_replace('[\r\n|\n]', '', $head);
		$head = preg_replace('[  ]', '', $head);
		$head = preg_replace('#	#', '', $head);

		$body = preg_replace('#<!--(\s+|.+)-->#', '', $body);
		$body = preg_replace('#	#', '', $body);
		$body = preg_replace('#(\s+)#', ' ', $body);

		$contents = $head . $body;

		return $contents;
	}


	/**
	 * @param $file
	 */
	protected function _include($file) {
		include $this->file_directory . $file;
	}

	/**
	 *
	 */
	public function ajaxPage() {
		$this->ajaxPage = true;
		$this->disable_main_file = true;
		$this->disable_layout_file = true;
	}

	////

	/**
	 * @param $var
	 * @return null
	 */
	public function __get($var) {
		return (array_key_exists($var, $this->store)) ? $this->store[$var] : null;
	}


	/**
	 * @param $var
	 * @param $value
	 */
	public function __set($var, $value) {
		$this->store[$var] = $value;
	}


	/**
	 * @param $name
	 * @param $arguments
	 * @return mixed
	 * @throws Exception
	 */
	public function __call($name, $arguments) {
		foreach ($this->helpers as $helper) {
			if (method_exists($helper, $name)) {
				return call_user_func_array(array($helper, $name), $arguments);
			}
		}

		throw new Exception('Unknown method: ' . $name);
	}

	/**
	 * @param $code string
	 */
	public function addHeaderCode($code) {
		$this->header_code .= $code;
	}

	/**
	 *
	 */
	protected function displayHeaderCode() {
		print $this->header_code;
	}

}


