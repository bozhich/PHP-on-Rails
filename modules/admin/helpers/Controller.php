<?php

abstract class Admin_ControllerHelper extends Core_Controller {
	public function init() {
		if (in_array($this->getRequest()->getRoute('action') . 'Action', get_class_methods($this))) {
			Admin_PermissionsHelper::checkPermissions();
		}
	}


	public function loginBeforeContinue() {
		if (!s()->user->id) {
			Core_Application::getInstance()->breakDispacher();
			$this->getView()->disableLayout();
			r('/');
		}
	}
}
