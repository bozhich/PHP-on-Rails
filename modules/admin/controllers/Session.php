<?php
class Admin_SessionController extends Admin_ControllerHelper {
	public function indexAction() {
		if ($this->getRequest()->getPost('user') == '' || $this->getRequest()->getPost('pass') == '') {
			$this->getResponse()->setStatus(0)->setBody(__('Enter username and password'))->toJson();
		}

		/*if (!Core_LinkExploit::checkOnce($this->getRequest()->getPost('token'), 'login')) {
			$this->getResponse()->setStatus(0)->setBody(__('Invalid Data Submitted'))->toJson();
		}*/

		$user_rs = Admin_UsersModel::get(array(
			'user'     => $this->getRequest()->getPost('user'),
			'password' => Core_Security::generate($this->getRequest()->getPost('pass')),
		));

		if (!$user_rs) {
			$this->getResponse()->setStatus(0)->setBody(__('Wrong username and/or password'))->toJson();
		}

		// now login the user
		if (Admin_UsersHelper::login($user_rs->id)) {
			$this->getResponse()->setStatus(1)->toJson();
		}
	}

	public function logoutAction() {
		if (s()->user->id) {
			s()->destroy();
			$this->getResponse()->setStatus(1)->redirect('admin')->toJson();
		}
	}
}
