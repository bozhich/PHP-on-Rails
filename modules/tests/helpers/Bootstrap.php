<?php

/**
 * Class Tests_BootstrapHelper
 */
class Tests_BootstrapHelper extends Core_Bootstrap {
	/**
	 *
	 */
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

	/**
	 * @throws Exception
	 */
	protected function loadCfg() {
		if (PHP_SAPI != 'cli') {
			p404('only cli');
		}
		$match_id = $this->getRequest()->getArgv(1);
		cfg()->load($match_id, Core_Cfg::MATCH_TYPE_ID);
	}

	/**
	 *
	 */
	protected function disableLayout() {
		$this->getView()->disableLayout();
	}
}
