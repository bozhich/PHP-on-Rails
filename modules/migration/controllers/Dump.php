<?php

/**
 * Class Migration_DumpController
 */
class Migration_DumpController extends Migration_ControllerHelper {


	/**
	 *
	 */
	public function indexAction() {
		die('todo');
		$overwrite = !is_null(Core_Request::getInstance()->getArgv(4)) ? Core_Request::getInstance()->getArgv(4) : false;

		foreach (dibi::getDatabaseInfo()->getTables() as $table_data) {
			if ($table_data->getName() == Migration_MigrationModel::getTableName()) {
				continue;
			}
			$ddl_data = dibi::query('SHOW CREATE TABLE ' . $table_data->getName())->fetch()->toArray();
			$ddl_query = $ddl_data['Create Table'];

			$migration_time = time();
			$migration_name = 'Create' . ucfirst($table_data->getName());

			$migration_name = str_replace(' ', '', $migration_name);
			$migration_name = str_replace('-', '', $migration_name);
			$migration_name = str_replace('_', '', $migration_name);
			$filename = cfg()->migration_path . $migration_name . '.php';

			if (Core_Files::fileSize($filename) && !$overwrite) {
				echo PHP_EOL . 'Migration "Create ' . ucfirst($table_data->getName()) . '" Exists' . PHP_EOL;
				continue;
			}

			$template_data = Core_Files::getContent(cfg()->migration_path . Migration_FilesHelper::TEMPLATE_FILE_NAME);
			$template_data = str_replace('Template', $migration_name, $template_data);
			$template_data = str_replace('__NAME__', $migration_name, $template_data);
			$template_data = str_replace('__CREATED_AT__', $migration_time, $template_data);
			$template_data = str_replace('__CREATED_CFG__', Core_Request::getInstance()->getArgv(1), $template_data);

			$template_data = preg_replace('#//__UP_ACTION__#', $ddl_query, $template_data);

			$down_query = 'DROP TABLE IF EXISTS `' . $table_data->getName() . '`';
			$template_data = preg_replace('#//__DOWN_ACTION__#', $down_query, $template_data);

			Core_Files::putContent($filename, $template_data);
			echo PHP_EOL . 'Migration ' . $filename . ' created' . PHP_EOL;
		}
	}
}