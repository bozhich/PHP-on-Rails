<?php

class Admin_RolesController extends Admin_ControllerHelper {
	public function indexAction() {
		$body = $this->getView()->fetch();
		$this->getResponse()->setBody($body)->toJson();
	}

	public function listAction() {
		$rs = Admin_RolesModel::getAll(array());
		$this->getView()->all = $rs;

		$body = $this->getView()->fetch();
		$this->getResponse()->setBody($body)->toJson();
	}

	public function saveAction() {
		$id = $this->getRequest()->getParam('id');
		if (!$id) {
			$this->getResponse()->setStatus(0)->setBody(__('role does not exist'))->toJson();
		}

		$role_rs = Admin_RolesModel::get(array('id' => $id));
		if (!$role_rs) {
			$this->getResponse()->setStatus(0)->setBody(__('role does not exist'))->toJson();
		}

		$update_data = array(
			'name'        => strip_tags($this->getRequest()->getPost('name')),
			'description' => strip_tags($this->getRequest()->getPost('desc')),
		);

		if ($update_data['name'] == '') {
			$this->getResponse()->setStatus(0)->setBody(__('invalid data submited. Role name is mandatory'))->toJson();
		}

		// check for duplicates
		$check_name = Admin_RolesModel::get(array('name' => $update_data['name']));
		if ($check_name && $check_name['id'] != $id) {
			$this->getResponse()->setStatus(0)->setBody(__('role with the same name exist'))->toJson();
		}

		Admin_RolesModel::set($update_data, array('id' => $id));

		$this->getResponse()->setStatus(1)->setBody(__('role updated'))->toJson();
	}

	public function deleteAction() {
		$id = $this->getRequest()->getParam('id');
		if (!$id) {
			$this->getResponse()->setStatus(0)->setBody(__('user does not exist'))->toJson();
		}

		$role_rs = Admin_RolesModel::get(array('id' => $id));
		if (!$role_rs) {
			$this->getResponse()->setStatus(0)->setBody(__('user does not exist'))->toJson();
		}

		if (is_null($this->getRequest()->getPost('confirmed'))) {
			$this->getResponse()->setStatus(2)->setBody(__('Are you sure?'))->toJson();
		}

		if ($role_rs->is_owner) {
			$this->getResponse()->setStatus(0)->setBody('how about no?')->toJson();
		}

		Admin_RolesModel::delete(array('id' => $id));

		$this->getResponse()->setStatus(1)->setBody(__('role deleted'))->toJson();
	}

	public function newAction() {
		$this->getView()->permissions = Admin_PermissionsModel::getAll();
		$body = $this->getView()->fetch();
		$this->getResponse()->setBody($body)->toJson();
	}

	public function createAction() {
		$insert_data = array(
			'name'        => strip_tags($this->getRequest()->getPost('name')),
			'description' => strip_tags($this->getRequest()->getPost('desc')),
		);

		if ($insert_data['name'] == '') {
			$this->getResponse()->setStatus(0)->setBody(__('invalid data submited. Role name is mandatory'))->toJson();
		}

		// check for duplicates
		$check_name = Admin_RolesModel::get(array('name' => $insert_data['name']));
		if ($check_name) {
			$this->getResponse()->setStatus(0)->setBody(__('role with the same name exist'))->toJson();
		}

		$permissions = $this->getRequest()->getPost('perm');
		if (empty($permissions)) {
			$this->getResponse()->setStatus(0)->setBody(__('role must have at least one permission'))->toJson();
		}

		$current_flag = 0;
		foreach ($permissions as $flag) {
			$current_flag = Core_Bit::add($current_flag, $flag);
		}

		$insert_data['permissions'] = $current_flag;

		Admin_RolesModel::add($insert_data);

		$this->getResponse()->setStatus(1)->setBody(__('role added'))->toJson();
	}

	public function permissionsAction() {
		$id = $this->getRequest()->getParam('id');
		if (!$id) {
			$this->getResponse()->setStatus(0)->setBody(__('role does not exist'))->toJson();
		}

		$role_rs = Admin_RolesModel::get(array('id' => $id));
		if (!$role_rs) {
			$this->getResponse()->setStatus(0)->setBody(__('role does not exist'))->toJson();
		}

		$return = array();
		$permissions_rs = Admin_PermissionsModel::getAll();
		foreach ($permissions_rs as $permission_row) {
			if (Core_Bit::check($role_rs->permissions, $permission_row->bit_flag) || $role_rs->is_owner) {
				$return[$permission_row['name']] = true;
			}
		}
		$this->getResponse()->setStatus(1)->setData(Core_Tools::JsonEncode($return))->toJson();
	}


}
