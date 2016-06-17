<?php

class Admin_PermissionsModel extends Core_Model {

	protected static $table = 'permissions';
	protected static $name_separator = ' : ';

	public function getFlag(array $data) {
		return $this->get($data, array('bit_flag'));
	}

	// different logic from the core add
	public function add(array $data, $table = false) {
		$data['bit_flag'] = (($this->getNextFlag() * 2) > 0) ? ($this->getNextFlag() * 2) : 1;
		$data['name'] =
			//ucfirst($data['module']) . self::$name_separator .
			ucfirst($data['controller'])
			. self::$name_separator . ucfirst($data['action']);


		parent::add($data);
	}

	public function getNextFlag() {
		$rs = $this->get(array(), array('max(bit_flag) AS max_flag'));

		return $rs['max_flag'];
	}

	// this get has different fetch method
	//public static function get($where) {
	//	return dibi::select('*')->from(self::$table)->where($where)->fetchAll();
	//}

	public function getAll($dummy = array()) {
		return dibi::select('*')->from(self::$table)->where('bit_flag > %i', 0)->fetchAll();
	}

}



