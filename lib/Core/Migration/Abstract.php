<?php

/**
 * Class Core_Migration_Abstract
 */
class Core_Migration_Abstract {
	const CREATED_AT = '';
	const NAME = '';
	/**
	 * @var Migration_MigrationModel
	 */
	protected $migration_model;

	/**
	 *
	 */
	public function __construct() {
		$this->migration_model = Migration_MigrationModel::getInstance();
		if (!$this->migration_model->checkSelfTable()) {
			$this->migration_model->createSelfTable();
		}
	}

	/**
	 * @return mixed
	 */
	public static function getCreationTime() {
		return static::CREATED_AT;
	}

	/**
	 * @return bool
	 */
	public function check() {
		return $this->shouldWeRun();
	}

	/**
	 * @return bool
	 */
	private function shouldWeRun() {
		$migration_data = $this->migration_model->get(array(
			'name' => static::NAME
		));
		if (!$migration_data) {
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
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

	/**
	 *
	 */
	public function up() {
	}

	/**
	 *
	 */
	private function insertRun() {
		$this->migration_model->add(array(
			'name' => static::NAME
		));
	}

	/**
	 *
	 */
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

	/**
	 *
	 */
	public function down() {
	}

	/**
	 *
	 */
	private function insertRollback() {
		Migration_MigrationModel::delete(array(
			'name' => static::NAME
		));
	}
}
