<?php

class Cms_UserData {
	const ATTR_ALL = 1;
	const ATTR_USER = 2;
	const ATTR_MESSAGES = 3;
	const ATTR_NOTIFICATIONS = 4;

	const CACHE_INJECT_PREFIX = 'refresh_user_data_';
	const CACHE_ACTIVITY_PREFIX = 'activity_user_data_'; // used to e stored in the cache

	protected static $all_attributes = array(
		self::ATTR_ALL,
		self::ATTR_USER,
		self::ATTR_MESSAGES,
		self::ATTR_NOTIFICATIONS,
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
			} elseif ($attr == self::ATTR_NOTIFICATIONS) {
				self::loadNotifications($user_id, $user_data);
			} elseif ($attr == self::ATTR_MESSAGES) {
				self::loadMessages($user_id, $user_data);
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


	/**
	 * @param $user_id
	 * @param $user_data
	 *
	 * When you're accessing $_SESSION, you're not just changing the current script's copy of the data read from
	 * the session, you're writing SafeString objects back into the active session.
	 * But putting custom objects in the session is dodgy and something I would generally try to avoid.
	 * To be able to do it you have to have defined the class in question before calling session_start;
	 * if you don't, PHP's session handler won't know how to deserialise the instances of that class, and you'll
	 * end up with the __PHP_Incomplete_Class Object.
	 * @http://stackoverflow.com/questions/2010427/php-php-incomplete-class-object-with-my-session-data
	 */
	protected static function loadMessages($user_id, &$user_data) {
		if (isset($user_data['messages'])) {
			unset($user_data['messages']);
		}

		$all_messages = Default_MessagesModel::getAll(array(
			'status'  => Const_Messages::STATUS_NEW,
			'to_user_id' => $user_id,
		));
		$user_data['messages']['new'] = count($all_messages);
	}

	/**
	 * @param $user_id
	 * @param $user_data
	 *
	 * When you're accessing $_SESSION, you're not just changing the current script's copy of the data read from
	 * the session, you're writing SafeString objects back into the active session.
	 * But putting custom objects in the session is dodgy and something I would generally try to avoid.
	 * To be able to do it you have to have defined the class in question before calling session_start;
	 * if you don't, PHP's session handler won't know how to deserialise the instances of that class, and you'll
	 * end up with the __PHP_Incomplete_Class Object.
	 * @http://stackoverflow.com/questions/2010427/php-php-incomplete-class-object-with-my-session-data
	 */
	protected static function loadNotifications($user_id, &$user_data) {
		if (isset($user_data['notifications'])) {
			unset($user_data['notifications']);
		}
		// Notifications
		// new notifications
		$all_notifications = Default_NotificationsModel::getAll(array(
			'status'  => Const_Notifications::STATUS_NEW,
			'user_id' => $user_id,
		));
		$user_data['notifications'] = array(); // Prevent notice
		$user_data['notifications']['records'] = array(); // Prevent notice

		$processed_notifications_count = 0;

		$new_notifications_item_ids = array();
		$new_notifications_count = 0;
		foreach ($all_notifications as $notification_rs) {
			if ($notification_rs['has_expire'] && strtotime($notification_rs['expire_timestamp']) < strtotime(Core_Model::getCurrentTime())) {
				continue;
			}

			$new_notifications_count++;
			$new_notifications_item_ids[$notification_rs->item_id][$notification_rs->type] = $notification_rs->item_id;
			$notification_obj = Cms_Notifications_Factory::getObj($notification_rs->type, $user_id, $notification_rs->item_id);

			if ($processed_notifications_count >= 3) {
				continue;
			}
			/* @var $notification_obj Cms_Notifications_Abstract */
			// not assiging the dibi row directly to the session ... read comment of method
			$user_data['notifications']['records'][] = array(
				'id'        => $notification_rs->id,
				'user_id'   => $notification_rs->user_id,
				'author_id' => $notification_rs->author_id,
				'item_id'   => $notification_rs->item_id,
				'type'      => $notification_rs->type,
				'status'    => $notification_rs->status,
				'timestamp' => $notification_rs->timestamp,
				'json_data' => $notification_rs->json_data,
				'text'      => $notification_obj->getText(),
			);
			$processed_notifications_count++;
		}
		$user_data['notifications']['new'] = $new_notifications_count;


		// old (if any)
		$old_notifications = Default_NotificationsModel::getAll(array(
			'status'  => Const_Notifications::STATUS_READ,
			'user_id' => $user_id,
		));
		foreach ($old_notifications as $notification_rs) {
			if ($processed_notifications_count >= 3) {
				continue;
			}
			if (isset($new_notifications_item_ids[$notification_rs->item_id][$notification_rs->type])) {
				continue;
			}

			$new_notifications_item_ids[$notification_rs->item_id][$notification_rs->type] = $notification_rs->item_id;
			$notification_obj = Cms_Notifications_Factory::getObj($notification_rs->type, $user_id, $notification_rs->item_id);

			// not assiging the dibi row directly to the session ... read comment of method
			$user_data['notifications']['records'][] = array(
				'id'        => $notification_rs->id,
				'user_id'   => $notification_rs->user_id,
				'author_id' => $notification_rs->author_id,
				'item_id'   => $notification_rs->item_id,
				'type'      => $notification_rs->type,
				'status'    => $notification_rs->status,
				'timestamp' => $notification_rs->timestamp,
				'json_data' => $notification_rs->json_data,
				'text'      => $notification_obj->getText(),
			);
			$processed_notifications_count++;
		}

	}

}
