<?php

class Default_View_BaseHelper {
	public function getStaticAddress() {
		return cfg()->static_address;
	}

	public function getRequest() {
		return Core_Request::getInstance();
	}

	public function getAvatar($user_id, $has_avatar, $thumb = true) {
		if ($has_avatar) {
			if ($thumb) {
				return cfg()->share_address . 'uploads/' . $user_id . '/thumb_' . Default_UsersController::AVATAR_FILE_NAME;
			} else {
				return cfg()->share_address . 'uploads/' . $user_id . '/' . Default_UsersController::AVATAR_FILE_NAME;
			}
		} else {
			return $this->getStaticAddress() . 'img/aatar_default.jpg';

		}
	}

	public function isLogged() {
		return s()->user->id;
	}

}


