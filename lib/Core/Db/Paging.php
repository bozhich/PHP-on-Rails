<?php

class Core_Db_Paging {
	protected $db;

	protected $query;

	protected $statement;

	protected $per_page;

	protected $current_page;

	protected $dump_mode = false;


	public function __construct(Core_Db $db, $query) {
		$this->db = $db;
		$this->query = $query;
	}


	public function limits($per_page, $current_page) {
		$this->per_page = (int) $per_page;
		$this->current_page = (int) $current_page;

		if ($this->per_page <= 0) {
			$this->per_page = 1;
		}

		if ($this->current_page <= 0) {
			$this->current_page = 1;
		}
	}


	public function setDumpMode() {
		$this->dump_mode = true;
	}


	public function get(array $input_parameters = array()) {
		// Calculate total results
		$stm_count = $this->db->prepare('SELECT COUNT(*) AS "cnt" FROM (' . $this->query . ') AS "rs"');
		if ($this->dump_mode) {
			$stm_count->setDumpMode();
		}
		$stm_count->execute($input_parameters);
		$count_rs = $stm_count->fetch();

		// Prepare statement
		$input_parameters['paging_limit'] = $this->per_page;
		$input_parameters['paging_offset'] = ($this->current_page - 1) * $this->per_page;

		$query_execute = 'SELECT * FROM (' . $this->query . ') AS "rs" LIMIT :paging_limit OFFSET :paging_offset';
		$this->statement = $this->db->prepare($query_execute);
		if ($this->dump_mode) {
			$this->statement->setDumpMode();
		}
		$this->statement->execute($input_parameters);

		return array(
			'results' => $this->statement->fetchAll(),
			'count'   => $count_rs['cnt'],
		);
	}
}
