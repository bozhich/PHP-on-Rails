<?php
/**
 * Class Core_Db
 */
require cfg()->root_path . 'vendor/autoload.php';
class Core_Db extends dibi {

	/**
	 * @param array $connectionParams
	 */
	public static function init(array $connectionParams) {
		if (!dibi::isConnected()) {
			try {
				$connection = dibi::connect(array(
					'driver'     => $connectionParams['driver'],
					'host'       => $connectionParams['host'],
					'dsn'        => 'mysql:host=' . $connectionParams['host'] . ';dbname=' . $connectionParams['db'] . '',
					'persistent' => true,
					'username'   => $connectionParams['user'],
					'password'   => $connectionParams['pass'],
					'database'   => $connectionParams['db'],
					'charset'    => isset($connectionParams['charset']) ? $connectionParams['charset'] : 'utf8',
					'result'     => array(
						'detectTypes'    => true,
						'formatDate'     => "Y-m-d",
						'formatDateTime' => 'Y-m-d H:i:s',
					),
//todo: used for mysql 
//					'options'    => array(
//						MYSQLI_OPT_CONNECT_TIMEOUT => 30
//					),
					'profiler'   => array(
						'run' => true,
					),
					'flags'      => MYSQLI_CLIENT_COMPRESS,
				));
				$panel = new Dibi\Bridges\Tracy\Panel;
				$panel->register($connection);
			} catch (DibiException $e) {
				$view = Core_View::getInstance();
				$view->setLayoutFile('$maintenance/db_connect.phtml');
				$view->displayLayout();
				die();
			}
		}
	}
}

