<?php

class Default_BootstrapHelper extends Core_Bootstrap {
	/**
	 *
	 */
	protected function loadCfg() {
		$domain = $this->getRequest()->getServer('SERVER_NAME') ? $this->getRequest()->getServer('SERVER_NAME') : $this->getRequest()->getServer('HTTP_HOST');
		cfg()->load($domain, Core_Cfg::MATCH_TYPE_DOMAIN, s()->xs->partner_id);
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
		$this->getView()->addHelper(new Default_View_BaseHelper());
		$this->getView()->addHelper(new Default_View_MessagesHelper());
	}

	/**
	 *
	 */
	protected function checkMaintenance() {
		if (cfg()->maintenance !== false) {
			$this->view->setLayoutFile('$maintenance/maintenance.phtml');
			$this->view->displayLayout();
			die();
		}
	}

	/**
	 *
	 */
	protected function singleSession() {
		if (!cfg()->dev_mode) {
			if (s()->user->id) {
				$user_data = Default_UsersModel::get(array(
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
	protected function setLanguage() {
		if (is_null($this->getRequest()->getRoute('lang')) || !isset(cfg()->language_codes[$this->getRequest()->getRoute('lang')])) {
			//foreach (cfg()->language_codes as $code => $name) {
			//	s()->misc->lang = $code;
			//	break;
			//}
		}
	}

	/**
	 *
	 */
	protected function userIdCookie() {
		// Cookie for memcache session miss rate
		$xss_user_id = $this->getRequest()->xss_user_id ? $this->getRequest()->xss_user_id : s()->user->id;
		if ($xss_user_id) {
			setcookie('xss_user_id', $xss_user_id, time() + Core_Session::PERSISTENT_TIME, $this->getRequest()->getHomeDir());
		}
	}

	protected function persistentLogin() {
		if (!s()->user->id) {
			// check for persistent session
			$cookie_token = $this->getRequest()->getCookie(Core_Session::NAME_PERSISTENT);
			// find a user with this token
			if ($cookie_token) {
				$user_rs = Default_UsersModel::get(array(
					'token' => $cookie_token,
				));
				if ($user_rs) {
					// refreshing persistent login
					Default_UsersHelper::login($user_rs->id, true);
				}
			}
		}
	}

	/**
	 *
	 */
	protected function setUserData() {
		if (s()->user->id) {
			// Refresh injection
			$cache_check = Cms_UserData::CACHE_INJECT_PREFIX . s()->user->id;
			if (Core_Cache::get($cache_check)) {
				Cms_UserData::refresh(Core_Cache::get($cache_check));
				Core_Cache::remove($cache_check);
			}
		}
	}

	protected function setLastActivity() {
		if (s()->user->id) {
			// Refresh injection
			$cache_check = Cms_UserData::CACHE_ACTIVITY_PREFIX . s()->user->id;
			if (!Core_Cache::get($cache_check)) {
				Default_UsersModel::set(array(
					'last_activity' => array('NOW()')
				), array(
					'id' => s()->user->id,
				));
				Core_Cache::set($cache_check, 1, cfg()->activity_period);
			}
		}
	}


	protected function initDebug() {
		if (class_exists('NDebugger')) {
			NDebugger::enable(NDebugger::DETECT, cfg()->error_path, cfg()->mail_user);
			//NDebugger::$logDirectory = cfg()->error_path;
			NDebugger::$strictMode = true;
		} else {
			Tracy\Debugger::enable(constant(cfg()->enviroment), cfg()->error_path);
		}
	}
}
