<?php

class Admin_TranslateController extends Admin_ControllerHelper {
	public function indexAction() {
		$body = $this->getView()->fetch();
		$this->getResponse()->setBody($body)->toJson();
	}

	public function listAction() {
		$rs = Admin_TranslateModel::getAll(array());
		$this->getView()->all = $rs;
		$body = $this->getView()->fetch();
		$this->getResponse()->setBody($body)->toJson();
	}

	public function saveAction() {
		$id = $this->getRequest()->getParam('id');
		if (!$id) {
			$this->getResponse()->setStatus(0)->setBody(__('tag does not exist'))->toJson();
		}

		$rs = Admin_TranslateModel::get(array('id' => $id));
		if (!$rs) {
			$this->getResponse()->setStatus(0)->setBody(__('tag does not exist'))->toJson();
		}

		$update_data = array(
			'value' => $this->getRequest()->getPost('value'),
		);
		Admin_TranslateModel::set($update_data, array('id' => $id));

		Cms_Translate::cacheSet($rs['tag_hash'], $this->getRequest()->getPost('value'));

		$this->getResponse()->setStatus(1)->setBody(__('Translate updated'))->toJson();
	}

}
