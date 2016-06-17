<?php

/**
 * Class Core_Response
 */
class Core_Response extends Core_Singleton {

	/**
	 * @var
	 */
	protected $status;
	/**
	 * @var
	 */
	protected $data;
	/**
	 * @var
	 */
	protected $body;
	/**
	 * @var
	 */
	protected $redirect;

	/**
	 *
	 */
	public function toJson() {
		$response = array(
			'status' => $this->getStatus(),
			'data'   => $this->getData(),
			'body'   => $this->getBody()
		);
		echo json_encode($response);
		die;
	}

	/**
	 * @return mixed
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @param mixed $status
	 * @return Core_Response
	 */
	public function setStatus($status) {
		$this->status = $status;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * @param mixed $data
	 * @return Core_Response
	 */
	public function setData($data) {
		$this->data = $data;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getBody() {
		return $this->body;
	}

	/**
	 * @param mixed $body
	 * @return Core_Response
	 */
	public function setBody($body) {
		$this->body = $body;

		return $this;
	}

	/**
	 * @param mixed $redirect
	 * @return Core_Response
	 */
	public function redirect($redirect) {
		$response = array(
			'redirect' => $redirect
		);
		echo json_encode($response);
		die;
	}

	/**
	 * @param bool $error_type
	 * @return $this
	 */
	public function setError($error_type = true) {
		$this->setStatus(0);
		$data = array(
			'error' => $error_type
		);
		$this->setData($data);

		return $this;
	}

	/**
	 * @param $error_text
	 * @return $this
	 */
	public function setErrorText($error_text) {
		$this->setStatus(0);
		$data = $this->getData();
		// Add error message to the currently loaded data
		$data['error_text'] = $error_text;
		// Assign back to DATA property
		$this->setData($data);

		return $this;
	}
}