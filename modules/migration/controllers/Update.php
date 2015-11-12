<?php

class Migration_UpdateController extends Migration_ControllerHelper {

	public function indexAction() {
		Core_Migration_Factory::update();
	}

}