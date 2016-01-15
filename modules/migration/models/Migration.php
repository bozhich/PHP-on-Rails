<?php

class Migration_MigrationModel extends Core_Model {
	protected static $table = 'migrations';

	public static function createSelfTable() {
		$query = 'CREATE TABLE `' . self::$table . '` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `name` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
				  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
		Core_Db::query($query);
	}

	public static function checkSelfTable() {
		try {
			Core_Db::getDatabaseInfo()->getTable(self::$table);
		} catch (DibiException $e) {
			return false;
		}

		return true;
	}

}
