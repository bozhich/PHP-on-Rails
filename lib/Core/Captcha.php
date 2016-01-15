<?php

/**
 * Class Core_Captcha
 */
class Core_Captcha extends Core_Singleton {

	const SECRET = 'XX';
	const SITE_KEY = 'YY';

	public static function check($captcha) {
		$curl = curl_init();
		$fields = array(
			'secret'   => self::SECRET,
			'response' => $captcha,
			'remoteip' => $_SERVER['REMOTE_ADDR']
		);

		$fields_string = '';
		foreach ($fields as $key => $value) {
			$fields_string .= $key . '=' . $value . '&';
		}
		rtrim($fields_string, '&');

		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT        => 600,
			CURLOPT_URL            => 'https://www.google.com/recaptcha/api/siteverify',
			CURLOPT_POST           => 1,
			CURLOPT_POSTFIELDS     => $fields_string,
		));

		$resp = curl_exec($curl);
		curl_close($curl);

		$resp = Core_Tools::JsonDecode($resp);

		return $resp['success'];
	}
}
