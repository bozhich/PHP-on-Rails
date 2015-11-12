<?php

class Migration_BootstrapHelper extends Core_Bootstrap {
	protected function setRoute() {
		/* @var $router Core_Request_Routes */
		$router = Core_Request_Routes::getInstance();
		$router->action = 'index'; // allways!!!!
	}

	protected function loadCfg() {
		if (PHP_SAPI != 'cli') {
			p404(' ');
		}
		$match_id = $this->getRequest()->getArgv(1);
		cfg()->load($match_id, Core_Cfg::MATCH_TYPE_ID);
	}

	/**
	 *
	 */
	protected function initDb() {
		Core_Db::init(cfg()->db_data);
	}

	protected function disableLayout() {
		Core_View::getInstance()->disableLayout();
	}
}
