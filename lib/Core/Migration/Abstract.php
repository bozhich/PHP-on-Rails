<?php

class Core_Migration_Abstract {
	/**
	 * @var Migration_MigrationModel
	 */
	protected $migration_model;

	public function __construct() {
		$this->migration_model = Migration_MigrationModel::getInstance();
		if (!$this->migration_model->checkSelfTable()) {
			$this->migration_model->createSelfTable();
		}
	}

	public function check() {
		return $this->shouldWeRun();
	}

	public function update() {
		if (!$this->shouldWeRun()) {
			return false;
		}

		$queries = $this->up();
		foreach ($queries as $query) {
			// @todo
			//Core_Db::query($query);
		}
		$this->insertRun();

		return true;
	}

	public function rollback() {
		$queries = $this->down();
		if (empty($queries)) {
			return;
		}
		foreach ($queries as $query) {
			Core_Db::query($query);
			$this->insertRollback();
		}
	}

	private function shouldWeRun() {
		$migration_data = $this->migration_model->get(array(
			'name' => static::NAME
		));
		if (!$migration_data) {
			return true;
		}

		return false;
	}

	private function insertRun() {
		$this->migration_model->add(array(
			'name' => static::NAME
		));
	}

	private function insertRollback() {
		Migration_MigrationModel::delete(array(
			'name' => static::NAME
		));
	}

	public function up() {
	}

	public function down() {
	}

	public static function getCreationTime() {
		return static::CREATED_AT;
	}
}
