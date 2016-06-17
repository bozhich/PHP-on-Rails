<?php

class Admin_BootstrapHelper extends Core_Bootstrap {
	protected $view;


	/**
	 *
	 */
	protected function loadCfg() {
		$domain = $this->getRequest()->getServer('SERVER_NAME') ? $this->getRequest()->getServer('SERVER_NAME') : $this->getRequest()->getServer('HTTP_HOST');
		cfg()->load($domain, Core_Cfg::MATCH_TYPE_DOMAIN, s()->xs->partner_id);
	}


	protected function removeLastSlash() {
		if ($_REQUEST['r'] == '/admin/') {
			r('/');
		}
	}


	/**
	 *
	 */
	protected function initView() {
		$this->view = Core_View::getInstance();
		$this->view->addHelper(new Admin_View_BaseHelper());
		$this->view->addHelper(new Admin_View_MessagesHelper());
		$this->view->addHelper(new Admin_View_PagingHelper());
	}


	/**
	 *
	 */
	protected function singleSession() {
		if (!cfg()->dev_mode) {
			if (s()->user->id) {
				$user_data = Admin_UsersModel::getInstance()->get(array(
					'id' => s()->user->id,
				));

				if ($user_data['session_id'] != s()->getId()) {
					s()->flush();
				}
			}
		}
	}


	/**
	 *
	 */
	protected function loadVersions() {
		$key = 'web_versions';
		$versions_rs = Core_Cache::get($key);
		if (!$versions_rs) {
			/**
			 * @var $versions_model Admin_VersionsModel
			 */
			$versions_model = Admin_VersionsModel::getInstance();
			$versions_rs = $versions_model->get();
			Core_Cache::set($key, $versions_rs, 5000);
		}
		foreach ($versions_rs as $key => $version_row) {
			if (cfg()->dev_mode && $version_row['is_dev']) {
				s()->version->current = $version_row;
				break;
			} else {
				s()->version->current = $version_row;
			}

		}
	}


}
