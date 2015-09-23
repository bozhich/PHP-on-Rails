<?php

class Default_SessionController extends Default_ControllerHelper {
	public function init() {
		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			$this->getView()->disableLayout();
		} else {
			p404('only ajax access here');
		}
	}

	public function refreshAction() {
		Cms_UserData::refresh(array(Cms_UserData::ATTR_ALL));
	}

	public function messagesAction() {
		Cms_UserData::refresh(array(Cms_UserData::ATTR_MESSAGES));
	}
}