<?php

/**
 * Class Core_Model
 */
abstract class Core_Model extends Core_Db {
	/**
	 * @param $data
	 * @param $where
	 * @return $this|DibiResult|FALSE|IDibiResultDriver|int|NULL
	 */
	public static function set($data, $where) {
		return dibi::update(static::$table, $data)->where($where)->execute();
	}

	/**
	 * @param $data
	 * @return array|bool|DibiRow|FALSE|mixed
	 */
	public static function get($data) {
		return dibi::select('*')->from(static::$table)->where($data)->fetch();
	}

	/**
	 * @param $data
	 * @return $this|DibiResult|FALSE|IDibiResultDriver|int|NULL
	 */
	public static function add($data) {
		return dibi::insert(static::$table, $data)->execute();
	}

	/**
	 * @param $data
	 * @return array
	 */
	public static function getAll($data) {
		return dibi::select('*')->from(static::$table)->where($data)->fetchAll();
	}

	/**
	 * @param $data
	 * @param $offset
	 * @param $limit
	 * @return array
	 */
	public static function getList($data, $offset, $limit, $order_field = '', $order_type = 'ASC') {
		if ($order_field != '') {
			return dibi::select('*')->from(static::$table)->where($data)->orderBy($order_field, $order_type)->fetchAll($offset, $limit);
		}

		return dibi::select('*')->from(static::$table)->where($data)->fetchAll($offset, $limit);
	}


	/**
	 * @param $where
	 * @return $this|DibiResult|FALSE|IDibiResultDriver|int|NULL
	 */
	public static function delete($where) {
		return dibi::delete(static::$table)->where($where)->execute();
	}

	/**
	 * @param $data
	 * @return $this|DibiResult|FALSE|IDibiResultDriver|int|NULL
	 */
	public static function addIgnore($data) {
		return dibi::insert(static::$table, $data)->setFlag('ignore', true)->execute();
	}


	/**
	 * @return mixed
	 */
	public static function getTableName() {
		return static::$table;
	}


	/**
	 * @return mixed
	 */
	public static function getCurrentTime() {
		$rs = dibi::select('NOW()')->as('time')->fetch();

		return $rs->time;
	}

	/**
	 * @param array $where
	 * @return mixed
	 */
	public static function getCount($where = array()) {
		return dibi::select('count(*)')->as('cnt')->from(static::$table)->where($where)->fetch();
	}

	/**
	 * @param $data
	 * @return $this|DibiResult|FALSE|IDibiResultDriver|int|NULL
	 */
	public static function tryAdd($data) {
		try {
			return dibi::insert(static::$table, $data)->execute();
		} catch (DibiDriverException $e) {
			//return $e->getMessage();
			false;
		}
	}
}

