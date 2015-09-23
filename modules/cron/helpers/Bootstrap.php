<?php

class Cron_BootstrapHelper extends Core_Bootstrap {
	protected function loadCfg() {
		$match_id = PHP_SAPI == 'cli' ? $this->getRequest()->getArgv(1) : $this->getRequest()->cfg_id;
		//cfg()->load($match_id, Core_Cfg::MATCH_TYPE_ID);
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
