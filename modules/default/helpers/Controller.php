<?php

abstract class Default_ControllerHelper extends Core_Controller {
	public function init() {
//		dd(s()->getAll());
	}


	public function loginBeforeContinue() {
		if (!s()->user->id) {
			Core_Application::getInstance()->breakDispacher();
			$this->getView()->disableLayout();
			$controller = $this->getRequest()->getRoute('controller');
			$action = $this->getRequest()->getRoute('action');
			$id = $this->getRequest()->getRoute('id');

			$backto = $controller . '/' . $action;
			if ($id !== null) {
				$backto .= '/' . $id;
			}
			r('users/login&back_to=' . urlencode($backto));
		}
	}

	public function userIsLogged() {
		if (s()->user->id) {
			return true;
		}

		return false;
	}
}
