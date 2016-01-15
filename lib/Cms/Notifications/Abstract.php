<?php

abstract class Cms_Notifications_Abstract {
	const TYPE_TIP_LIKE = 1;

	protected static $type_to_setting = array(
		self::TYPE_TIP_LIKE           => Const_Settings::SETTING_ENABLE_LIKE_NOTIFICATION,
	);

	abstract function getType();

	abstract function getText();

	public static function add($to_user_id, $type, $author_id, $item_id, $json_data = array(), $expire_timestamp = false) {
		if (!in_array($type, self::getTypes())) {
			return false;
		}

		// check if user has setting enabled to receive notifications
		if (!Default_SettingsHelper::getStatus(self::$type_to_setting[$type], $to_user_id)) {
			return false;
		}

		$row_data = array(
			'user_id'   => $to_user_id,
			'type'      => $type,
			'status'    => Const_Notifications::STATUS_NEW,
			'author_id' => $author_id,
			'item_id'   => $item_id,
			'json_data' => Core_Tools::JsonEncode($json_data)
		);

		if ($expire_timestamp) {
			$row_data['has_expire'] = 1;
			$row_data['expire_timestamp'] = $expire_timestamp;
		}

		Default_NotificationsModel::add($row_data);
	}

	public static function remove($to_user_id, $type, $author_id, $item_id) {
		if (!in_array($type, self::getTypes())) {
			return false;
		}

		// check if user has setting enabled to receive notifications
		if (!Default_SettingsHelper::getStatus(self::$type_to_setting[$type], $to_user_id)) {
			return false;
		}

		Default_NotificationsModel::delete(array(
			'user_id'   => $to_user_id,
			'type'      => $type,
			'author_id' => $author_id,
			'item_id'   => $item_id
		));
	}

	public static function getTypes() {
		return array(
			self::TYPE_TIP_LIKE,
			self::TYPE_TIP_EXP_GAINED,
			self::TYPE_SLIP_LIKE,
			self::TYPE_NEW_LEVEL,
			self::TYPE_NEW_FOLLOWER,
			self::TYPE_BIRTHDAY,
			self::TYPE_HOTTEST_GAMES,
			self::TYPE_EDITOR_PICK_GAME,
			self::TYPE_FOLLOW_NEW_COMMENT,
			self::TYPE_FOLLOW_NEW_SLIP,
		);
	}
}
