<?php

class Admin_IndexController extends Admin_ControllerHelper {
	public function indexAction() {
		// Empty for 1st render
	}

	public function loadAction() {
		if (s()->user->id) {
			$body = $this->getView()->fetch();
		} else {
			$body = $this->getView()->fetch('session/index.phtml');
		}

		$this->getResponse()->setBody($body)->toJson();
	}
}
