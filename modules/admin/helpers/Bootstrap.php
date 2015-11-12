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
	protected function initDb() {
		Core_Db::init(cfg()->db_data);
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
				$user_data = Admin_UsersModel::get(array(
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

	protected function initDebug() {
		if (class_exists('NDebugger')) {
			NDebugger::enable(NDebugger::DETECT, cfg()->error_path, cfg()->mail_user);
			//NDebugger::$logDirectory = cfg()->error_path;
			NDebugger::$strictMode = true;
		} else {
			Tracy\Debugger::enable(constant(cfg()->enviroment));
		}
	}




	/**
	 *
	 */
	protected function loadVersions() {
		$key = 'web_versions';
		$versions_rs = Core_Cache::get($key);
		if (!$versions_rs) {
			$versions_rs = Admin_VersionsModel::getAll(array());
			Core_Cache::set($key, $versions_rs, 5000);
		}
		foreach ($versions_rs as $key => $version_row) {
//			if ($version_row['name'] == $this->getRequest()->getRoute('version')) {
//				s()->version->current = $version_row;
//				unset($versions_rs[$key]);
//				break;
//			}
			if (cfg()->dev_mode && $version_row['is_dev']) {
				s()->version->current = $version_row;
				break;
			} else {
				s()->version->current = $version_row;
			}

		}
	}


}
