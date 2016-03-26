<?php

class Migration_CheckController extends Core_Controller {
	public function indexAction() {
		Core_Migration_Factory::check();
	}

}