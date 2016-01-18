<?php

class Tests_AuthFile extends PHPUnit_Framework_TestCase {

	public function testInvaludLogin() {
		/* @var $curl Core_Curl */
		$curl = Core_Curl::getInstance();
		$curl->setUrl(cfg()->site_address);
		$rs = $curl->send(array(
			'auth',
			'index'
		), array(
			'user' => 'testInvalid',
			'pass' => 'invalidPassword'
		));
		$response = json_decode($rs);
		$this->assertEquals(0, $response->status);
	}

	public function testValidLogin() {
		/* @var $curl Core_Curl */
		$curl = Core_Curl::getInstance();
		$curl->setUrl(cfg()->site_address);
		$rs = $curl->send(array(
			'auth',
			'index'
		), array(
			'user' => 'luka',
			'pass' => '123456'
		));
		$response = json_decode($rs);
		$this->assertEquals(1, $response->status);

		$user_data = Default_PlayersModel::get(array(
			'user' => 'luka',
		));

		$this->assertEquals($user_data->session_id, $response->data->token);
	}
}
