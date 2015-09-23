<?php
//require_once('Nette/Debugger.php');
//require_once('dibi.php');
//require_once('bridges/Tracy/IBarPanel.php');
//require_once('bridges/Tracy/Bar.php');
//require_once('bridges/Tracy/DefaultBarPanel.php');
//require_once('bridges/Tracy/BlueScreen.php');
//require_once('bridges/Tracy/ILogger.php');
//require_once('bridges/Tracy/Debugger.php');
//require_once('bridges/Tracy/Panel.php');
//require_once('bridges/Tracy/Helpers.php');
//require_once('bridges/Tracy/Dumper.php');
//require_once('bridges/Tracy/FireLogger.php');

/**
 * Class Core_Db
 */
class Core_Db extends dibi {

	/**
	 * @param array $connectionParams
	 */
	public static function init(array $connectionParams) {
		if (!dibi::isConnected()) {
			try {
				$connection = dibi::connect(array(
					'driver'   => $connectionParams['driver'],
					'host'     => $connectionParams['host'],
					'username' => $connectionParams['user'],
					'password' => $connectionParams['pass'],
					'database' => $connectionParams['db'],
					'charset'  => isset($connectionParams['charset']) ? $connectionParams['charset'] : 'utf8',
					'result'   => array(
						'detectTypes'    => true,
						'formatDate'     => "Y-m-d",
						'formatDateTime' => 'Y-m-d H:i:s',
					),
					'options'  => array(
						MYSQLI_OPT_CONNECT_TIMEOUT => 30
					),
					'profiler' => array(
						'run' => true,
					),
					'flags'    => MYSQLI_CLIENT_COMPRESS,
				));

				// add panel to debug bar
				$panel = new Dibi\Bridges\Tracy\Panel;
				$panel->register($connection);
			} catch (DibiException $e) {
				echo get_class($e), ': ', $e->getMessage(), "\n";
				die;
			}
		}
	}
}

