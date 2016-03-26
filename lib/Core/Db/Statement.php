<?php

class Core_Db_Statement extends PDOStatement {
	protected $db;

	protected $dump_mode = false;


	protected function __construct(Core_Db $db) {
		$this->db = $db;
		$this->setFetchMode(PDO::FETCH_ASSOC);
	}


	public function dump() {
		$this->dump_mode = true;
	}


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
					$dump_sql = str_replace(':' . $param, '<strong><i>' . $value . '</i></strong>', $dump_sql);
				}
			}
			print '<pre>' . $dump_sql . '</pre>';
		}

		try {
			$start_time = Core_Db_Log::getStartTime();

			$result = parent::execute($input_parameters);

			Core_Db_Log::log($this->queryString, $input_parameters, $start_time);
		} catch (Exception $e) {
			Core_ErrorLog::save(Core_ErrorLog::TYPE_DB, $e->getMessage());
			p404($e->getMessage(), 'dbError');
		}

		return $result;
	}
}
