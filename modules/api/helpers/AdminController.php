<?php

abstract class Api_AdminControllerHelper extends Api_ControllerHelper {
	protected $response = array(
		'errors' => array(),
		'data'   => array(),
	);


	public function init() {
		parent::init();
	}


	public function __destruct() {
		if ($this->getView()->reason) {
			return;
		} elseif (!empty($this->response['errors'])) {
			$reponse = array('status' => 0, 'message' => $this->response['errors']);
		} else {
			$reponse = array('status' => 1, 'data' => $this->response['data']);
		}

		if (in_array(cfg()->getId(), array('luka_', 'tim')) && cfg()->dev_mode) {
			d($reponse);
		} else {
			print json_encode($reponse);
		}
		die();
	}


	protected function response($response) {
		print json_encode($response, JSON_FORCE_OBJECT);
	}


	protected function checkParams(array $parameters) {
		foreach ($parameters as $id) {
			if (!isset($this->getRequest()->{$id}) || $this->getRequest()->{$id} == '') {
				$this->addError('Missing param: ' . $id);
				die();
			}
		}
	}


	protected function addError($error) {
		$this->response['errors'] = $error;
	}


	protected function addResponse($data = array()) {
		$this->response['data'] = $data;
	}
}
