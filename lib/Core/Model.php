<?php

/**
 * Class Core_Model
 */
abstract class Core_Model extends Core_Singleton {
	/**
	 * @var bool
	 */
	protected static $table = false;

	/**
	 * @var Core_Db
	 */
	private static $db;


	/**
	 *
	 */
	public function __construct() {
		if (!isset(self::$db)) {
			self::$db = new Core_Db(cfg()->db_data['host'], cfg()->db_data['user'], cfg()->db_data['pass'], cfg()->db_data['db'], cfg()->db_data['driver']);
		}
	}

	/**
	 * @param array $data
	 * @param bool  $table
	 */
	public function add(array $data, $table = false) {
		if (!$table) {
			$table = static::$table;
		}
		// extract and quote col names from the array keys
		$cols = array();
		$vals = array();
		foreach ($data as $col => $val) {
//			$cols[] = $this->quoteIdentifier($col, true);
			$cols[] = $col;
			$vals[] = ':' . $col;
		}
		// build the statement
		$sql = "INSERT INTO "
			. $table
			. ' (' . implode(', ', $cols) . ') '
			. 'VALUES (' . implode(', ', $vals) . ')';

		$stm = $this->getDb()->prepare($sql);
		$stm->execute($data);
	}

	/**
	 * @return Core_Db
	 */
	public function getDb() {
		return self::$db;
	}

	/**
	 * @param int $length
	 * @return string
	 */
	public function getRandomString($length = 6) {
		$stm = $this->getDb()->prepare("SELECT
					array_to_string(ARRAY(
							SELECT substr(
									'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
									trunc(random() * 62)::INTEGER + 1,
									1
									)
								FROM
								generate_series (1, :length)
							),
						''
					) as string;");
		$stm->execute(array(
			'length' => $length
		));

		return $stm->fetchColumn();
	}

	/**
	 * @param $schema
	 * @param $table
	 * @return string
	 */
	public function checkTableExist($schema, $table) {
		if (cfg()->db_driver == 'pgsql') {
			$stm = $this->getDb()->prepare("SELECT
											count(tablename)
										FROM
											pg_catalog.pg_tables
										WHERE
											tablename = :table_name
										AND schemaname = :schema_name;");
			$stm->execute(array(
				'table_name'  => $table,
				'schema_name' => $schema
			));
		} else {
			$stm = $this->getDb()->prepare("SHOW TABLES LIKE :table_name");
			$stm->execute(array(
				'table_name'  => $table,
			));
		}
		return $stm->fetchColumn();
	}

	/**
	 * @param array $where
	 * @param array $select
	 * @return array
	 */
	public function get($where = array(), $select = array('*'), $table = false) {
		if (!$table) {
			$table = static::$table;
		}

		$query = "SELECT " . implode(',', $select) . "
				    FROM
						" . $table . "
					WHERE 1 = 1";

		$params = array();
		foreach ($where as $col => $val) {
			$query .= ' AND ' . $this->quoteIdentifier($col) . ' = :' . $col;
			$params[$col] = $val;
		}

		$stm = $this->getDb()->prepare($query);
		$stm->execute($params);

		return $stm->fetch();
	}

	/**
	 * @param $value
	 * @return string
	 */
	protected function quoteIdentifier($value) {
		$q = '"';

		return ($q . str_replace("$q", "$q$q", $value) . $q);
	}

	/**
	 * @param array $where
	 * @param array $select
	 * @return array
	 */
	public function getAll($where = array(), $select = array('*'), $table = false) {
		if (!$table) {
			$table = static::$table;
		}

		$query = "SELECT " . implode(',', $select) . "
				    FROM
						" . $table . "
					WHERE 1 = 1";

		$params = array();
		foreach ($where as $col => $val) {
			$query .= ' AND ' . $this->quoteIdentifier($col) . ' = :' . $col;
			$params[$col] = $val;
		}

		$stm = $this->getDb()->prepare($query);
		$stm->execute($params);
		return $stm->fetchAll();
	}

	/**
	 * @param       $table
	 * @param array $data
	 * @param array $where
	 * @return int affected rows
	 */
	protected function sqlSet($table, array $data, array $where) {
		$binds = array();
		$sql = 'UPDATE ' . $table . ' SET ';
		foreach ($data as $field => $value) {
			$sql .= '"' . $field . '" = :' . $field . ', ';
			$binds[$field] = $value;
		}
		$sql = substr($sql, 0, -2);
		$sql .= ' WHERE ';
		foreach ($where as $field => $value) {
			$sql .= '"' . $field . '" = :' . $field . ' AND ';
			$binds[$field] = $value;
		}
		$sql = substr($sql, 0, -5);

		$stm = $this->getDb()->prepare($sql);
		$stm->execute($binds);

		return $stm->rowCount();
	}
}

