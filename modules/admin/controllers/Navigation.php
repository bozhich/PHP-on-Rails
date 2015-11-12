<?php

class Admin_NavigationController extends Admin_ControllerHelper {
	public function indexAction() {
		if (s()->user->id) {
			$body = $this->getView()->fetch();
		} else {
			$body = '';
		}
		$this->getResponse()->setBody($body)->toJson();
	}
}
