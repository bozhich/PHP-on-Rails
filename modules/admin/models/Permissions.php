<?php

class Admin_PermissionsModel extends Core_Model {

	protected static $table = 'permissions';
	protected static $name_separator = ' : ';

	public static function getFlag(array $data) {
		return dibi::select('bit_flag')
			->from(self::$table)
			->where($data)
			->fetchSingle();
	}

	// different logic from the core add
	public static function add($data) {
		$data['bit_flag'] = ((self::getNextFlag() * 2) > 0) ? (self::getNextFlag() * 2) : 1;
		$data['name'] =
			//ucfirst($data['module']) . self::$name_separator .
			ucfirst($data['controller'])
			. self::$name_separator . ucfirst($data['action']);

		return dibi::insert(self::$table, $data)->execute();
	}

	public static function getNextFlag() {
		return dibi::select('max(bit_flag)')->from(self::$table)->fetchSingle();
	}

	// this get has different fetch method
	//public static function get($where) {
	//	return dibi::select('*')->from(self::$table)->where($where)->fetchAll();
	//}

	public static function getAll($dummy = array()) {
		return dibi::select('*')->from(self::$table)->where('bit_flag > %i', 0)->fetchAll();
	}

}



