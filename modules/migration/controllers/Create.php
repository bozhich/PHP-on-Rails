<?php

class Migration_CreateController extends Migration_ControllerHelper {
	public function indexAction() {
		$migration_time = time();
		$migration_name = Core_Request::getInstance()->getArgv(4);

		if (!$migration_name) {
			throw new Exception('No migration name given');
		}

		$migration_name = str_replace(' ', '', $migration_name);
		$migration_name = str_replace('-', '', $migration_name);
		$migration_name = str_replace('_', '', $migration_name);
		$migration_name = ucfirst(strtolower($migration_name));
		$filename = cfg()->migration_path . $migration_name . '.php';

		if (Core_Files::fileSize($filename)) {
			throw new Exception('Migration with that name exists');
		}

		$template_data = Core_Files::getContent(cfg()->migration_path . Migration_FilesHelper::TEMPLATE_FILE_NAME);
		$template_data = str_replace('Template', $migration_name, $template_data);
		$template_data = str_replace('__NAME__', $migration_name, $template_data);
		$template_data = str_replace('__CREATED_AT__', $migration_time, $template_data);
		$template_data = str_replace('__CREATED_CFG__', Core_Request::getInstance()->getArgv(1), $template_data);

		Core_Files::putContent($filename, $template_data);
		echo PHP_EOL . 'Migration ' . $filename . ' created' . PHP_EOL . PHP_EOL;
	}

}