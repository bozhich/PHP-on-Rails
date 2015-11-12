<?php

/**
 * Class Admin_View_MessagesHelper
 */
class Admin_View_MessagesHelper {
	protected static $messages = array();


	/**
	 * @param        $msg
	 * @param string $type
	 */
	public function addMessage($msg, $type = 'info') {
		$this->setData($msg, $type, self::$messages);
	}


	/**
	 * @param        $msg
	 * @param string $type
	 */
	public function addFlashMessage($msg, $type = 'info') {
		$messages = s()->view_messages->flash;
		$this->setData($msg, $type, $messages);
		s()->view_messages->flash = $messages;
	}


	/**
	 * @return array
	 */
	public function getMessages() {
		if (s()->view_messages->flash) {
			foreach (s()->view_messages->flash as $row) {
				self::$messages[] = $row;
			}
			unset(s()->view_messages->flash);
		}

		return self::$messages;
	}

	/**
	 * @param $msg
	 * @param $type
	 * @param $messages
	 */
	protected function setData($msg, $type, &$messages) {
		if (is_array($msg)) {
			foreach ($msg as $m) {
				$messages[] = array(
					'type' => $type,
					'msg'  => $m,
				);
			}
		} else {
			$messages[] = array(
				'type' => $type,
				'msg'  => $msg,
			);
		}
	}
}
