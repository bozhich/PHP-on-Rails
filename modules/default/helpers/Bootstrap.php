<?php


/**
 * Class Default_BootstrapHelper
 */
class Default_BootstrapHelper extends Core_Bootstrap {
	/**
	 *
	 */
	protected function loadCfg() {
		$domain = $this->getRequest()->getServer('SERVER_NAME') ? $this->getRequest()->getServer('SERVER_NAME') : $this->getRequest()->getServer('HTTP_HOST');
		cfg()->load($domain, Core_Cfg::MATCH_TYPE_DOMAIN);
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
		$ip = $this->getRequest()->getServer('REMOTE_ADDR');
		if (cfg()->maintenance && !in_array($ip, cfg()->dev_ips)) {
			$this->getView()->setLayoutFile('$maintenance/maintenance.phtml');
			$this->getView()->displayLayout();
			die();
		}
	}


	/**
	 *
	 */
	protected function persistentLogin() {
		if (!s()->user->id) {
			// check for persistent session
			$cookie_token = $this->getRequest()->getCookie(Core_Session::NAME_PERSISTENT);
			// find a user with this token
			if ($cookie_token) {
			/*	$users_module  = Default_PlayersModel::getInstance();
				$user_rs = $users_module->get(array(
					'token' => $cookie_token,
				));
				if ($user_rs) {
					// refreshing persistent login
					Default_PlayersHelper::login($user_rs->id, true);
				}*/
			}
		}
	}

	/* Dev env only */
	/**
	 *
	 */
	protected function getMigrationStatistics() {
		if (cfg()->dev_mode) {
			$migration_stats = Core_Migration_Factory::check(true);
			Core_Debug::getInstance()->getBar()->addCollector(new DebugBar\DataCollector\MessagesCollector('Migrations'));
			if (count($migration_stats) > 0) {
				Core_Debug::getInstance()->getBar()['Migrations']->info('php ' . cfg()->root_path . 'index.php ' . cfg()->getId() . ' migration update');
				Core_Debug::getInstance()->getBar()['Migrations']->info($migration_stats);
			} else {
				Core_Debug::getInstance()->getBar()['Migrations']->info('Up to date');
			}
		}
	}
}
