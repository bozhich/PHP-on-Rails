<?php

/**
 * Class Admin_PermissionsHelper
 */
class Admin_PermissionsHelper {
	/**
	 *
	 */
	public static function checkPermissions() {
		$request = Core_Request::getInstance();
		// permissions structure
		$data = array(
			'module'     => $request->getRoute('module'),
			//'lang' => $request->getRoute('lang'),
			'controller' => $request->getRoute('controller'),
			'action'     => $request->getRoute('action'),
		);
		$flag = Admin_PermissionsModel::getFlag($data);

		// $flag = 0 - is a free acces of the page
		if ($flag === 0) {
			return true;
		}

		if (!$flag) {
			// we need to check that method exist
			Admin_PermissionsModel::add($data);
		} else {
			if (!s()->user->id) {
				// @todo
				Core_View::getInstance()->addFlashMessage(__('Please Login'), 'danger');
				Core_Response::getInstance()->setStatus(1)->redirect('admin')->toJson();
			}
			if (!Core_Bit::check(s()->user->access['permissions'], $flag)) {
				// well an owner has ALL access
				$role_rs = Admin_RolesModel::get(array('id' => s()->user->role_id));
				if ($role_rs->is_owner === 1 || s()->user->is_developer === 1) {
					return true;
				}
				Core_View::getInstance()->addFlashMessage(__('You Don\'t have permission to access this page'), 'danger');
				Core_Response::getInstance()->setStatus(1)->redirect('admin')->toJson();
			}
		}
	}
}
