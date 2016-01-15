<?php

class Cms_Notifications_Factory {
	protected static $notifications_to_obj = array(
		Cms_Notifications_Abstract::TYPE_TIP_LIKE           => 'Cms_Notifications_TipLike',
		Cms_Notifications_Abstract::TYPE_TIP_EXP_GAINED     => 'Cms_Notifications_TipExpGained',
		Cms_Notifications_Abstract::TYPE_SLIP_LIKE          => 'Cms_Notifications_SlipLike',
		Cms_Notifications_Abstract::TYPE_NEW_LEVEL          => 'Cms_Notifications_NewLevel',
		Cms_Notifications_Abstract::TYPE_NEW_FOLLOWER       => 'Cms_Notifications_NewFollower',
		Cms_Notifications_Abstract::TYPE_BIRTHDAY           => 'Cms_Notifications_Birthday',
		Cms_Notifications_Abstract::TYPE_HOTTEST_GAMES      => 'Cms_Notifications_HottestGames',
		Cms_Notifications_Abstract::TYPE_EDITOR_PICK_GAME   => 'Cms_Notifications_EditorPickGame',
		Cms_Notifications_Abstract::TYPE_FOLLOW_NEW_COMMENT => 'Cms_Notifications_FollowNewComment',
		Cms_Notifications_Abstract::TYPE_FOLLOW_NEW_SLIP    => 'Cms_Notifications_FollowNewSlip',
	);

	protected static $instances = array();

	public static function getObj($type, $user_id, $item_id) {
		if (!isset(self::$notifications_to_obj[$type])) {
			return false;
		}

		if (!isset(self::$instances[$type][$item_id])) {
			self::$instances[$type][$item_id] = new self::$notifications_to_obj[$type]($user_id, $item_id);
		}

		return self::$instances[$type][$item_id];

	}
}
