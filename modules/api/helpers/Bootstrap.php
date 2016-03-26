<?php

class Api_BootstrapHelper extends Core_Bootstrap {
	protected function loadCfg() {
		$domain = $this->getRequest()->getServer('SERVER_NAME') ? $this->getRequest()->getServer('SERVER_NAME') : $this->getRequest()->getServer('HTTP_HOST');
		cfg()->load($domain, Core_Cfg::MATCH_TYPE_DOMAIN);
	}

	protected function disableLayout() {
		Core_View::getInstance()->disableLayout();
	}

	/**
	 *
	 */
	protected function initView() {
		$this->view = Core_View::getInstance();
	}
}