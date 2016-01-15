<?php

/**
 * Class Events_Type_Abstract
 */
abstract class Cms_Events_Type_Abstract {
	/**
	 * @var array
	 */
	protected $_data = array();

	/**
	 * @var bool
	 */
	public static $eventType = false;


	/**
	 *
	 */
	const STATUS_PENDING = 1;
	/**
	 *
	 */
	const STATUS_IN_PROGRESS = 2;
	/**
	 *
	 */
	const STATUS_COMPLETED = 3;
	/**
	 *
	 */
	const STATUS_FAILED = 4;

	/**
	 *
	 */
	const STATUS_CANCELED = 5;

	/**
	 *
	 */
	const EVENT_TYPE_TEST = 1;

	protected static $requirements = array(
		'name'           => 'NAME',
		'details'        => array(
			'from_date' => 'datepick',
			'to_date'   => 'datepick',
			'comp_id'   => 'select',
		),
		'execution_time' => true,
	);

	/**
	 * @return mixed
	 */
	abstract function execute();


	/**
	 *
	 */
	public function setInProgress() {
		$this->__set('status', self::STATUS_IN_PROGRESS);
		$this->_save();
	}


	/**
	 *
	 */
	public function setPending() {
		$this->__set('status', self::STATUS_PENDING);
		$this->_save();
	}


	/**
	 *
	 */
	public function setCompleted() {
		$this->__set('status', self::STATUS_COMPLETED);
		$this->__set('executed_at', array('NOW()'));
		$this->_save();
	}

	/**
	 *
	 */
	public function setCanceled() {
		$this->__set('status', self::STATUS_CANCELED);
		$this->_save();
	}


	/**
	 *
	 */
	public function setFailed() {
		$this->__set('status', self::STATUS_FAILED);
		$this->_save();
	}


	/**
	 * @throws Events_Exception
	 */
	private function _save() {
		Default_EventsModel::set($this->_data, array('id' => $this->id));
	}


	/**
	 * @return int
	 */
	private function _add() {
		return Default_EventsModel::add($this->_data);
	}


	/**
	 * @param $data
	 */
	public function setData($data) {
		$this->_data = $data;
	}


	/**
	 * @param $param
	 * @return mixed
	 */
	public function __get($param) {
		return $this->_data[$param];
	}


	/**
	 * @param array $eventData
	 * @return int
	 */
	public function addEvent(array $eventData) {
		$eventData['type'] = static::$eventType;
		$eventData['status'] = Cms_Events_Type_Abstract::STATUS_PENDING;
		$eventData['priority'] = isset($eventData['priority']) ? $eventData['priority'] : 0;

		if (array_key_exists('json_data', $eventData) && is_array($eventData['json_data'])) {
			$eventData['json_data'] = json_encode($eventData['json_data']);
		}

		if (!isset($eventData['execution_time'])) {
			$eventData['execution_time'] = array('NOW()');
		}

		$this->setData($eventData);

		return $this->_add();
	}


	/**
	 * @param $param
	 * @param $value
	 * @return mixed
	 */
	public function __set($param, $value) {
		return $this->_data[$param] = $value;
	}


	/**
	 * @return array
	 */
	public static function getEventsType() {
		return array(
			self::EVENT_TYPE_TEST => 'Test',
		);
	}


	/**
	 * @return array
	 */
	public static function getEventsStatuses() {
		return array(
			self::STATUS_PENDING     => 'Pending',
			self::STATUS_IN_PROGRESS => 'In Progress',
			self::STATUS_COMPLETED   => 'Completed',
			self::STATUS_FAILED      => 'Failed',
		);
	}


	/**
	 * @param int    $after
	 * @param string $unit
	 */
	protected function _prepareNextEventSchedule($after = 1, $unit = 'DAY') {
		$eventData = array(
			'execution_time' => array('NOW() + INTERVAL ' . $after . ' ' . $unit),
		);

		Cms_Events_Type_Factory::getObject(static::$eventType)->addEvent($eventData);
	}


	/**
	 * @return mixed
	 */
	protected function _getJsonData() {
		return json_decode($this->json_data, 1);
	}

	/**
	 * @return array
	 */
	public static function getRequirements() {
		return static::$requirements;
	}
}

