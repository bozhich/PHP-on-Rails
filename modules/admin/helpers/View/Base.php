<?php

class Admin_View_BaseHelper {
	public function getStaticAddress() {
		return cfg()->static_address;
	}

	public function getRequest() {
		return Core_Request::getInstance();
	}

	public function getAvatar($user_id, $has_avatar, $thumb = true) {
		if ($has_avatar) {
			if ($thumb) {
				return cfg()->share_address . $user_id . '/avatar/thumb/' . $user_id . '.jpg';
			} else {
				return cfg()->share_address . $user_id . '/avatar/' . $user_id . '.jpg';
			}
		} else {
			return $this->getStaticAddress() . 'img/defaultProfilePic.jpg';

		}
	}

	public function getLeagueImage($league, $has_avatar, $thumb = true) {
		if ($has_avatar) {
			if ($thumb) {
				return $this->getStaticAddress() . '/img/leagues/' . strtolower($league) . '_thumb.png';
			} else {
				return $this->getStaticAddress() . '/img/leagues/' . strtolower($league) . '.png';
			}
		} else {
			return $this->getStaticAddress() . 'img/leagues/wood.png';
		}
	}

	public function isLogged() {
		return s()->user->id;
	}

	public function hasPermissions($for_where) {
		if (strstr($for_where, '/')) {
			list($controller, $action) = explode('/', $for_where);
		} else {
			$controller = $for_where;
			$action = 'index';
		}
		$data = array(
			'module'     => $this->getRequest()->getRoute('module'),
			'controller' => $controller,
			'action'     => $action,
		);

		// flag must not be 0
		return Core_Bit::check(s()->user->access['permissions'], Admin_PermissionsModel::getFlag($data));
	}

	public function isAdmin() {
		return s()->user->access['is_owner'] ? true : false;
	}
}


