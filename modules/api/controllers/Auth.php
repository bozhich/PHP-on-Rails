<?php

class Api_AuthController extends Api_AdminControllerHelper {
	public function indexAction() {
		$user = $this->getRequest()->user;
		$pass = $this->getRequest()->pass;

		$player_rs = Default_PlayersModel::get(array(
			'user'     => $user,
			'password' => Core_Security::generate($pass),
		));

		if (!$player_rs) {
			return $this->addError('Invalid username and/or password');
		}

		$token = Default_UsersHelper::login($player_rs->id);
		$this->addResponse(array(
			'token' => $token
		));
	}
}