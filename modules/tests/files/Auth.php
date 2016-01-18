<?php
class Tests_AuthController extends Tests_BootstrapHelper {
	public function indexAction() {
		foreach (get_class_methods(__CLASS__) as $method) {
			if (strstr('test', $method)) {
				$this->{$method};
			}
		}
	}

	public function testEmpty()
	{
		$stack = array();
		$this->assertEmpty($stack);

		return $stack;
	}


	public function testInvaludLoginAction() {
		$data = http_build_query(array(
			'user' => 'testInvalid',
			'pass' => 'invalidPassword'
		));
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "http://starlet.dev/api/auth/index");
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		$response = json_decode(curl_exec($curl));
		curl_close($curl);
		Core_Unit::getInstance()->assertEquals(0, $response->status);
	}

	public function testValidLogin() {
		$data = http_build_query(array(
			'user' => 'luka',
			'pass' => '123456'
		));
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "http://starlet.dev/api/auth/index");
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		$response = json_decode(curl_exec($curl));
		curl_close($curl);
		Core_Unit::getInstance()->assertEquals(1, $response->status);

	}
}
