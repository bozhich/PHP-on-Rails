<?php

class Tests_BootstrapHelper extends Core_Bootstrap {
	protected function setRoute() {
		/* @var $router Core_Request_Routes */
		$router = Core_Request_Routes::getInstance();

		$routes = array(
			1 => array('name' => 'test'),
			2 => array('name' => 'controller', 'empty_value' => 'index'),
			3 => array('name' => 'action', 'empty_value' => 'index'),
			4 => array('name' => 'id'),
		);
		$router->setRoutes($routes);
	}

	protected function loadCfg() {
		$match_id = PHP_SAPI == 'cli' ? $this->getRequest()->getArgv(1) : $this->getRequest()->cfg_id;
		cfg()->load($match_id, Core_Cfg::MATCH_TYPE_ID);
		Core_View::getInstance()->disableLayout();

		if (PHP_SAPI != 'cli') {
			p404('only cli');
		}
	}

	protected function initDb() {
		Core_Db::init(cfg()->db_data);
	}

	protected function disableLayout() {
		$this->getView()->disableLayout();
	}
}
