<?php

abstract class Api_ControllerHelper extends Core_Controller {
	protected $allowed_ips = array(
		array('127.0.0.0', '127.0.0.255'), // localhost
		array('5.101.103.177', '5.101.103.177'), // localhost
		array('46.47.76.240', '46.47.76.240'), // localhost
		array('141.101.0.0', '141.101.255.225'),
		//141.101.64.113
	);


	public function init() {
		if (isset($this->do_not_check_ip) && $this->do_not_check_ip) {
			return;
		}
		$client_ip = ip2long($this->getRequest()->getServer('REMOTE_ADDR'));
		$has_access = false;
		foreach ($this->allowed_ips as $row) {
			list($ip_from, $ip_to) = $row;

			if ($client_ip >= ip2long($ip_from) && $client_ip <= ip2long($ip_to)) {
				$has_access = true;
			}
		}
		if (!$has_access) {
			p404('invalid ip');
		}
	}


}
