<?php

class Core_Migration_Abstract {

	public function __construct() {
		if (!Migration_MigrationModel::checkSelfTable()) {
			Migration_MigrationModel::createSelfTable();
		}
	}

	public function check() {
		return $this->shouldWeRun();
	}

	public function update() {
		if (!$this->shouldWeRun()) {
			return false;
		}

		$query = $this->up();
		Core_Db::query($query);
		$this->insertRun();

		return true;
	}

	public function rollback() {
		$query = $this->down();
		if (strlen($query) > 20) {
			Core_Db::query($query);
			$this->insertRollback();
		}
	}

	private function shouldWeRun() {
		$migration_data = Migration_MigrationModel::get(array(
			'name' => static::NAME
		));
		if (!$migration_data) {
			return true;
		}

		return false;
	}

	private function insertRun() {
		Migration_MigrationModel::add(array(
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
