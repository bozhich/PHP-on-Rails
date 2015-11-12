<?php

class Admin_UsersController extends Admin_ControllerHelper {
	public function indexAction() {
		$body = $this->getView()->fetch();
		$this->getResponse()->setBody($body)->toJson();
	}

	public function listAction() {
		$users_rs = Admin_UsersModel::getAll(array());

		foreach ($users_rs as &$user_row) {
			$user_row['role'] = Admin_RolesModel::get(array('id' => $user_row['role_id']));
			$user_row['last_active'] = Cms_Time::label($user_row['last_active']);
		}

		$this->getView()->all_roles = Admin_RolesModel::getAll(array());

		$this->getView()->all = $users_rs;
		$body = $this->getView()->fetch();
		$this->getResponse()->setBody($body)->toJson();
	}

	public function saveAction() {
		$id = $this->getRequest()->getParam('id');
		if (!$id) {
			$this->getResponse()->setStatus(0)->setBody(__('user does not exist'))->toJson();
		}

		$user_rs = Admin_UsersModel::get(array('id' => $id));
		if (!$user_rs) {
			$this->getResponse()->setStatus(0)->setBody(__('user does not exist'))->toJson();
		}

		if ($id != s()->user->id && $user_rs->is_developer) {
			$this->getResponse()->setStatus(0)->setBody('how about no?')->toJson();
		}


		$update_data = array(
			'user'    => strip_tags(strtolower($this->getRequest()->getPost('user'))),
			'email'   => strip_tags(strtolower($this->getRequest()->getPost('email'))),
			'role_id' => $this->getRequest()->getPost('role'),
			'company' => $this->getRequest()->getPost('company'),
		);

		if ($update_data['user'] == '' || $update_data['email'] == '' || $update_data['role_id'] == '') {
			$this->getResponse()->setStatus(0)->setBody(__('invalid data submited. Username, Email and Role are mandatory'))->toJson();
		}

		// check for duplicates on user/ email
		$check_username = Admin_UsersModel::get(array('user' => $update_data['user']));
		if ($check_username && $check_username['id'] != $id) {
			$this->getResponse()->setStatus(0)->setBody(__('user with the same name exist'))->toJson();
		}

		$check_email = Admin_UsersModel::get(array('email' => $update_data['email']));
		if ($check_email && $check_email['id'] != $id) {
			$this->getResponse()->setStatus(0)->setBody(__('user with the same email exist'))->toJson();
		}

		Admin_UsersModel::set($update_data, array('id' => $id));

		// Refresh the user's session
		if ($id == s()->user->id) {
			Cms_UserData::refresh(array(Cms_UserData::ATTR_ALL));
		} else {
			Cms_UserData::injectRefresh($id, array(Cms_UserData::ATTR_ALL));
		}
		$this->getResponse()->setStatus(1)->setBody(__('user updated'))->toJson();
	}

	public function deleteAction() {
		$id = $this->getRequest()->getParam('id');
		if (!$id) {
			$this->getResponse()->setStatus(0)->setBody(__('user does not exist'))->toJson();
		}

		$user_rs = Admin_UsersModel::get(array('id' => $id));
		if (!$user_rs) {
			$this->getResponse()->setStatus(0)->setBody(__('user does not exist'))->toJson();
		}

		if (is_null($this->getRequest()->getPost('confirmed'))) {
			$this->getResponse()->setStatus(2)->setBody(__('Are you sure?'))->toJson();
		}

		if ($id != s()->user->id && $user_rs->is_developer) {
			$this->getResponse()->setStatus(0)->setBody('how about no?')->toJson();
		}

		Admin_UsersModel::delete(array('id' => $id));

		// Refresh the user's session
		if ($id == s()->user->id) {
			Cms_UserData::refresh(array(Cms_UserData::ATTR_ALL));
		} else {
			Cms_UserData::injectRefresh($id, array(Cms_UserData::ATTR_ALL));
		}

		$this->getResponse()->setStatus(1)->setBody(__('user deleted'))->toJson();
	}

	public function newAction() {
		$this->getView()->all_roles = Admin_RolesModel::getAll(array());

		$body = $this->getView()->fetch();
		$this->getResponse()->setBody($body)->toJson();
	}

	public function createAction() {

		$email = strtolower($this->getRequest()->getPost('email'));
		if (!Core_Check::email($email)) {
			$this->getResponse()->setStatus(0)->setBody(__('invalid email'))->toJson();
		}

		$pass = $this->getRequest()->getPost('pass');
		if (!Core_Check::password($pass)) {
			$this->getResponse()->setStatus(0)->setBody(__('password must be at least %1$s characters long', cfg()->min_pass_lenght))->toJson();
		}

		$user = $this->getRequest()->getPost('user');
		if (!Core_Check::user($user)) {
			$this->getResponse()->setStatus(0)->setBody(__('invalid user'))->toJson();
		}

		$all_roles = Admin_RolesModel::getAll(array());
		$role_id = $this->getRequest()->getPost('role');
		$valid_role = false;
		foreach ($all_roles as $role_row) {
			if ($role_id == $role_row->id) {
				$valid_role = true;
			}
		}

		if (!$valid_role) {
			$this->getResponse()->setStatus(0)->setBody(__('invalid role'))->toJson();
		}

		$insert_data = array(
			'user'     => strip_tags(strtolower($this->getRequest()->getPost('user'))),
			'email'    => strip_tags(strtolower($this->getRequest()->getPost('email'))),
			'role_id'  => $this->getRequest()->getPost('role'),
			'password' => Core_Security::generate($this->getRequest()->getPost('pass')),
			'company'  => $this->getRequest()->getPost('company'),
		);

		if ($insert_data['user'] == '' || $insert_data['email'] == '' || $insert_data['role_id'] == '') {
			$this->getResponse()->setStatus(0)->setBody(__('invalid data submited. Username, Email and Role are mandatory'))->toJson();
		}

		if (!Admin_UsersModel::tryAdd($insert_data)) {
			$this->getResponse()->setStatus(0)->setBody(__('duplicate user or email'))->toJson();
		}
		$this->getResponse()->setStatus(1)->setBody(__('user added'))->toJson();
	}

}
