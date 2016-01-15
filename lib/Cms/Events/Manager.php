<?php

class Cms_Events_Manager extends Core_Singleton {
	/**
	 * @var bool
	 */
	private $_eventObject = false;


	/**
	 * @param $id
	 * @return bool
	 */
	public function loadEvent($id) {
		$evenData = Default_EventsModel::get(array(
			'id' => $id,
		));
		if ($evenData['status'] != Cms_Events_Type_Abstract::STATUS_PENDING) {
			// do some loging
			return false;
		}

		$this->_eventObject = Cms_Events_Type_Factory::getObject($evenData['type']);
		if ($this->_eventObject) {
			$this->_eventObject->setData($evenData);
			//if (cfg()->dev_mode) {
				// TODO... cuz I'm an asshole... Some dev shit...
			//} else {
				$this->_eventObject->setInProgress();
			//}

			return true;
		}

		return false;
	}


	/**
	 * @return bool
	 */
	public function executeEvent() {
		if (!$this->_eventObject) {
			return false;
		}
		try {
			$exec_result = $this->_eventObject->execute();
			$this->_eventObject->setCompleted();

			return $exec_result;
		} catch (Events_Exception $e) {
			if ($e->getCode() == Cms_Events_Exception::EXCEPTION_CODE_RETRY) {
				$this->_eventObject->setPending();
				ApplicationCore::notifyGameProblem('Error In Event schedule. setting status to pending', 0);
			} else {
				$this->_eventObject->setFailed();
			}

			return false;
		}
	}

	/**
	 * @return bool
	 */
	public function cancelExecution() {
		if (!$this->_eventObject) {
			return false;
		}

		$this->_eventObject->setCanceled();
	}

}
