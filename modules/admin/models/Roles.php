<?php

class Admin_RolesModel extends Core_Model {
	protected static $table = 'panel_roles';

	/**
	 * @param $data
	 * @return array
	 */
	public static function getAllAssoc($data) {
		return dibi::select('*')->from(static::$table)->where($data)->fetchAssoc('id');
	}
}

