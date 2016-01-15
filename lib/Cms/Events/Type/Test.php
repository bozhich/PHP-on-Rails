<?php

/*
 * @info
 * data['json_data'] = {"msg":{"luka":"bg_BG","bg":"en_EN","hr":"en_EN","mom4":"en_EN","nemci":"de_DE"}}
 */

class Cms_Events_Type_Test extends Cms_Events_Type_Abstract {
	public static $eventType = self::EVENT_TYPE_TEST;

	public function execute() {
		$this->_prepareNextEventSchedule();
		d('well.. ti works');
	}

}