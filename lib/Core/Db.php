<?php

/**
 * Class Core_Db
 */
class Core_Db extends PDO {

	/**
	 * @param        $host
	 * @param        $user
	 * @param        $pass
	 * @param        $name
	 * @param string $driver
	 */
	public function __construct($host, $user, $pass, $name, $driver = 'pgsql') {
		try {
			parent::__construct($driver . ':dbname=' . $name . ';host=' . $host, $user, $pass);
		} catch (Exception $e) {
			if (!cfg()->dev_mode && Core_Request::getInstance()->getRoute('module') == 'default') {
				$view = Core_View::getInstance();
				$view->setLayoutFile('$maintenance/db_connect.phtml');
				$view->displayLayout();
				die();
			} else {
				print get_class($e) . ': ' . $e->getMessage() . PHP_EOL;
				die();
			}
		}


		if (cfg()->dev_mode) {
			$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} else {
			$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
		}

		$this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('Core_Db_Statement', array($this)));
		if (cfg()->dev_mode) {
//			Core_Debug::getInstance()->initPdoDebug($this);
		}
	}


	/**
	 * @param $query
	 * @return Core_Db_Paging
	 */
	public function paging($query) {
		return new Core_Db_Paging($this, $query);
	}
}
