<?php

/**
 * Class Migration_UpdateController
 */
class Migration_UpdateController extends Core_Controller {

	/**
	 *
	 */
	public function indexAction() {
		Core_Migration_Factory::update();
	}

}