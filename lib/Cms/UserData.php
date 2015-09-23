<?php

class Cms_UserData {
	const ATTR_ALL = 1;
	const ATTR_USER = 2;
	const ATTR_MESSAGES = 3;
	const ATTR_SETTINGS = 4;

	const CACHE_INJECT_PREFIX = 'refresh_user_data_';
	const CACHE_ACTIVITY_PREFIX = 'activity_user_data_'; // used to e stored in the cache

	protected static $all_attributes = array(
		self::ATTR_ALL,
		self::ATTR_USER,
	);


	/**
	 * @param array $attributes
	 */
	public static function refresh(array $attributes) {
		$user_id = s()->user->id;
		$user_data = s()->getAll();
		self::get($user_id, $attributes, $user_data);
		s()->setAll($user_data);
	}


	/**
	 * @param       $user_id
	 * @param array $attributes
	 * @param null  $user_data
	 * @return array|null|void
	 */
	public static function get($user_id, array $attributes, &$user_data = null) {
		if (!$user_id) {
			return;
		}

		if (in_array(self::ATTR_ALL, $attributes)) {
			$attributes = self::$all_attributes;
		}

		if (!isset($user_data)) {
			$user_data = array();
		}

		foreach ($attributes as $attr) {
			if ($attr == self::ATTR_USER) {
				self::loadUser($user_id, $user_data);
			}
		}

		return $user_data;
	}


	/**
	 * @param       $user_id
	 * @param array $attributes
	 */
	public static function injectRefresh($user_id, array $attributes) {
		$cache_id = self::CACHE_INJECT_PREFIX . $user_id;
		if (Core_Cache::get($cache_id)) {
			$attributes = array_merge($attributes, Core_Cache::get($cache_id));
		}
		Core_Cache::set($cache_id, $attributes, 180);

	}


	/**
	 * @param $user_id
	 * @param $user_data
	 */
	protected static function loadUser($user_id, &$user_data) {
		if (isset($user_data['user'])) {
			unset($user_data['user']);
		}

		$user_data['user'] = Core_Tools::toArray(Default_UsersModel::get(array(
			'id' => $user_id,
		)));

		// Fix Language
		if (!$user_data['user']['language_code']) {
			foreach (cfg()->language_codes as $code => $name) {
				Default_UsersModel::set(array(
					'language_code' => $code,
				), array(
					'id' => $user_id,
				));
				$user_data['user']['language_code'] = $code;
				break;
			}
		}
	}
}
