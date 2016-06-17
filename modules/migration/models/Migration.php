<?php

/**
 * Class Migration_MigrationModel
 */
class Migration_MigrationModel extends Core_Model {
	/**
	 * @var string
	 */
	protected static $table = 'migrations';

	/**
	 * Create the initial migrations table
	 */
	public function createSelfTable() {
		if (cfg()->db_driver == 'pgsql') {
			$query = 'CREATE TABLE IF NOT EXISTS ' . self::$table . ' (
						"id" serial4 NOT NULL,
						"name" varchar,
						"timestamp" timestamp DEFAULT CURRENT_TIMESTAMP,
						PRIMARY KEY ("id")
						) WITH (OIDS=FALSE);';
		} else {
			$query = "CREATE TABLE ".self::$table." (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `name` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
					  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					  `executed` tinyint(4) DEFAULT '0',
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB;";
		}
		return $this->query($query);
	}

	/**
	 * This is only used in migrations - that's why it's placed ONLY here!
	 * Do not use it for ANY other reasons.
	 * @param $query
	 * @return bool
	 */
	public function query($query) {
		return $this->getDb()->query($query)->execute();
	}

	/**
	 * @return string
	 */
	public function checkSelfTable() {
		if (cfg()->db_driver == 'pgsql') {
			list($schema, $table) = explode('.', self::$table);
		} else {
			list($schema, $table) = array('.', self::$table);
		}

		return $this->checkTableExist($schema, $table);
	}
}
