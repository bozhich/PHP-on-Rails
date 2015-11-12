<?php

class Migration_TemplateFile extends Core_Migration_Abstract  {

	const NAME = '__NAME__';
	const CREATED_AT = '__CREATED_AT__';
	const CREATED_IN_CFG = '__CREATED_CFG__';

	public function up() {
		$query = "//__UP_ACTION__";
		return $query;
	}

	public function down() {
		$query = "//__DOWN_ACTION__";
		return $query;
	}
}