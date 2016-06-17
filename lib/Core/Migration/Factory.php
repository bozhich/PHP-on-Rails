<?php

/**
 * Class Core_Migration_Factory
 */
class Core_Migration_Factory {

	/**
	 *
	 */
	const MIGRATION_PREFIX = 'Migration_';
	/**
	 *
	 */
	const MIGRATION_SUFIX = 'File';
	/**
	 *
	 */
	const TEMPLATE_NAME = 'Template.php';

	/**
	 *
	 */
	public static function update() {
		$must_migrate = false;
		$migrations_list = array();
		foreach (Core_Files::listFiles(cfg()->migration_path) as $migration_file) {
			if (strstr($migration_file, self::TEMPLATE_NAME)) {
				continue;
			}
			$migration_data = explode(DS, $migration_file);

			list($migration_file_name) = array_reverse($migration_data);
			list($migration_name, $extension) = explode('.', $migration_file_name);

			$migration_class_name = self::MIGRATION_PREFIX . $migration_name . self::MIGRATION_SUFIX;
			/* @var $migration_object Core_Migration_Abstract */
			$migrations_list[$migration_class_name] = $migration_class_name::getCreationTime();
		}

		asort($migrations_list);
		foreach ($migrations_list as $migration_class_name => $time_created) {
			$migration_object = new $migration_class_name();
			try {
				if ($migration_object->update()) {
					$must_migrate = true;
					echo 'UPDATED: ' . $migration_class_name . PHP_EOL;
				}
			} catch (DibiException $e) {
				echo 'ROLLING BACK: ' . $migration_class_name . PHP_EOL;
				$migration_object->rollback();
				die;
			}
		}

		if (!$must_migrate) {
			echo 'DB is UP TO DATE' . PHP_EOL;
		}
	}


	/**
	 * @param bool $return
	 * @return array
	 */
	public static function check($return = false) {
		$must_migrate = false;
		$return_data = array();
		foreach (Core_Files::listFiles(cfg()->migration_path) as $migration_file) {
			if (strstr($migration_file, self::TEMPLATE_NAME)) {
				continue;
			}
			$migration_data = explode(DS, $migration_file);

			list($migration_file_name) = array_reverse($migration_data);
			list($migration_name, $extension) = explode('.', $migration_file_name);

			$migration_class_name = self::MIGRATION_PREFIX . $migration_name . self::MIGRATION_SUFIX;
			/* @var $migration_object Core_Migration_Abstract */
			$migration_object = new $migration_class_name();
			if ($migration_object->check()) {
				$must_migrate = true;
				if ($return) {
					$return_data[] = $migration_file_name;
				} else {
					echo 'TO RUN: ' . $migration_file_name . PHP_EOL;
				}
			}
		}
		if (!$must_migrate) {
			if (!$return) {
				echo 'DB is UP TO DATE' . PHP_EOL;
			}
		}

		if ($return) {
			return $return_data;
		}
	}
}
