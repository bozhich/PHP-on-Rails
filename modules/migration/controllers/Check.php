<?php

class Migration_CheckController extends Migration_ControllerHelper {
	public function indexAction() {
		Core_Migration_Factory::check();
	}

}