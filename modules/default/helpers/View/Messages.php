<?php

class Default_View_MessagesHelper {
	private $msg_to_css_class_map = array(
		'error' => 'danger',
		'info'  => 'info'
	);

	protected static $messages = array();


	public function addMessage($msg, $type = 'info') {
		$this->setData($msg, $type, self::$messages);
	}

	public function addFlashMessage($msg, $type = 'info') {
		$messages = s()->view_messages->flash;
		$this->setData($msg, $type, $messages);
		s()->view_messages->flash = $messages;
	}

	public function getMessages() {
		if (s()->view_messages->flash) {
			foreach (s()->view_messages->flash as $row) {
				self::$messages[] = $row;
			}
			unset(s()->view_messages->flash);
		}

		return self::$messages;
	}

	protected function setData($msg, $type, &$messages) {
		if (is_array($msg)) {
			foreach ($msg as $m) {
				$messages[] = array(
					'type'      => $type,
					'msg'       => $m,
					'css_class' => ($this->msg_to_css_class_map[$type]) ? ($this->msg_to_css_class_map[$type]) : $type
				);
			}
		} else {
			$messages[] = array(
				'type'      => $type,
				'msg'       => $msg,
				'css_class' => ($this->msg_to_css_class_map[$type]) ? ($this->msg_to_css_class_map[$type]) : $type
			);
		}
	}

}
