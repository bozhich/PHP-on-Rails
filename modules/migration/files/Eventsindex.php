<?php

class Migration_EventsindexFile extends Core_Migration_Abstract  {

	const NAME = 'Eventsindex';
	const CREATED_AT = 1452092137;
	const CREATED_IN_CFG = 'luka';

	public function up() {
		$query = "ALTER TABLE `events`
ADD INDEX (`status`) ,
ADD INDEX (`execution_time`) ;";
		return $query;
	}

	public function down() {
		$query = "//__DOWN_ACTION__";
		return $query;
	}
}
