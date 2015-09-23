<?php

class Default_TranslateModel extends Core_Model {
	protected static $table = 'translate';

	public static function setNotFound(array $hashes) {
		// clear previous data
		$data = array(
			'not_found_date' => null
		);
		Core_Db::update(self::$table, $data)->execute();

		// set the new not found
		$data = array(
			'not_found_date' => array('NOW()')
		);
		Core_Db::update(self::$table, $data)->where('tag_hash NOT IN (', implode(',', $hashes), ')')->execute();
	}
}
