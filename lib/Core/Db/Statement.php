<?php

/**
 * Class Core_Db_Statement
 */
class Core_Db_Statement extends PDOStatement {
	/**
	 * @var Core_Db
	 */
	protected $db;

	/**
	 * @var bool
	 */
	protected $dump_mode = false;


	/**
	 * @param Core_Db $db
	 */
	protected function __construct(Core_Db $db) {
		$this->db = $db;
		$this->setFetchMode(PDO::FETCH_OBJ);
	}


	/**
	 *
	 */
	public function dump() {
		$this->dump_mode = true;
	}


	/**
	 * @param null $input_parameters
	 * @return bool
	 */
	public function execute($input_parameters = null) {
		// Bool PDO bug
		if (is_array($input_parameters)) {
			foreach ($input_parameters as &$value_param) {
				if (is_bool($value_param)) {
					$value_param = $value_param ? 't' : 'f';
				}
			}
		}

		if ($this->dump_mode) {
			$dump_sql = $this->queryString;
			if (is_array($input_parameters)) {
				foreach ($input_parameters as $param => $value) {
					$dump_sql = str_replace(':' . $param, '\'<strong><i>' . $value . '</i></strong>\'', $dump_sql);
				}
			}
			print '<pre>' . $dump_sql . '</pre>';
		}

		try {
			$start_time = Core_Db_Log::getStartTime();

			$result = parent::execute($input_parameters);

			//@todo - 2nd PDO instance
//			Core_Db_Log::log($this->queryString, $input_parameters, $start_time);
		} catch (Exception $e) {
			Core_ErrorLog::save(Core_ErrorLog::TYPE_DB, $e->getMessage());
			p404($e->getMessage(), 'dbError');
		}

		return $result;
	}
}
