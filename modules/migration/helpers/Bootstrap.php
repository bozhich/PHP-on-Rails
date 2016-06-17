<?php

/**
 * Class Migration_BootstrapHelper
 */
class Migration_BootstrapHelper extends Core_Bootstrap {
	/**
	 *
	 */
	protected function setRoute() {
		/* @var $router Core_Request_Routes */
		$router = Core_Request_Routes::getInstance();
		$router->action = 'index'; // allways!!!!
	}

	/**
	 * @throws Exception
	 */
	protected function loadCfg() {
		if (PHP_SAPI != 'cli') {
			p404('not found');
		}
		$match_id = $this->getRequest()->getArgv(1);
		cfg()->load($match_id, Core_Cfg::MATCH_TYPE_ID);
	}

	/**
	 *
	 */
	protected function disableLayout() {
		Core_View::getInstance()->disableLayout();
	}
}
