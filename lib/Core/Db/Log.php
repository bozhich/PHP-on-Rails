<?php

class Core_Db_Log {
	protected static $session_id;

	protected static $instance;


	public static function getStartTime() {
		return microtime();
	}


	protected static function getDb() {
		if (!isset(self::$instance)) {
			self::$instance = new PDO('mysql:host=' . cfg()->db_log['host'] . ';dbname=' . cfg()->db_log['name'], cfg()->db_log['user'], cfg()->db_log['pass']);
			self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			self::$instance->query('SET NAMES utf8');
		}

		return self::$instance;
	}


	public static function log($query, $parameters, $start_time) {
		if (!isset(self::$session_id)) {
			$session_id = md5(microtime());
		}

		$execution_time = microtime() - $start_time;
		$hash = md5($query);

		$query_log = $query;
		if (is_array($parameters)) {
			foreach ($parameters as $param => $value) {
				$query_log = str_replace(':' . $param, $value, $query_log);
			}
		}

		Core_Debug::getInstance()->getBar()->addCollector(new DebugBar\DataCollector\MessagesCollector('PDO'));
		Core_Debug::getInstance()->getBar()['PDO']->info($query_log);

		if (!cfg()->db_log) {
			return;
		}
		$stm = self::getDb()->prepare('INSERT INTO `' . cfg()->db_log['table'] . '` SET
			`hash` = :hash,
			`hash_with_params` = :hash_with_params,
			`query` = :query,
			`time` = :time,
			`session_id` = :session_id,
			`url` = :url
		');
		$stm->execute(array(
			'hash'             => $hash,
			'hash_with_params' => md5($query_log),
			'query'            => trim($query_log),
			'time'             => $execution_time,
			'session_id'       => $session_id,
			'url'              => !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null,
		));
	}
}
