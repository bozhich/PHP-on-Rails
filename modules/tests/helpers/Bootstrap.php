<?php

class Tests_BootstrapHelper extends Core_Bootstrap {
	protected function loadCfg() {
		$match_id = PHP_SAPI == 'cli' ? $this->getRequest()->getArgv(1) : $this->getRequest()->cfg_id;
		cfg()->load($match_id, Core_Cfg::MATCH_TYPE_ID);
		Core_View::getInstance()->disableLayout();

		if (PHP_SAPI != 'cli') {
			p404('only cli sapi');
		}
	}

	protected function initDb() {
		Core_Db::init(cfg()->db_data);
	}

	protected function disableLayout() {
		$this->getView()->disableLayout();
	}
}
