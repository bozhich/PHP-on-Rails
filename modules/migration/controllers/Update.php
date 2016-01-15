<?php

class Migration_UpdateController extends Cron_ControllerHelper {

	public function indexAction() {
		Core_Migration_Factory::update();
	}

}