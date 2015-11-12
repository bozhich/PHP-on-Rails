<?php

class Admin_UsersHelper extends Admin_ControllerHelper {

	public static function login($user_id, $persistent = false) {
		s()->flush();

		if ($persistent) {
			s()->setPersistent();
		}

		// Load user data
		s()->user->id = $user_id;

		// Set user session
		Admin_UsersModel::set(array(
				'session_id' => s()->getId(),
			), array(
				'id' => s()->user->id,
			)
		);

		Admin_LogsModel::insert(array(
			'user_id' => $user_id,
			'ip'      => Core_Request::getInstance()->getServer('REMOTE_ADDR'),
		), Admin_LogsModel::USER_LOGIN);

		Cms_UserData::refresh(array(Cms_UserData::ATTR_ALL));

		return (s()->user->id) ? true : false;
	}
}