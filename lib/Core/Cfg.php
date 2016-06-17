<?php

/**
 * Class Core_Cfg
 */
class Core_Cfg extends Core_Singleton {
	/**
	 *
	 */
	const MATCH_TYPE_DOMAIN = 1;

	/**
	 *
	 */
	const MATCH_TYPE_ID = 2;

	/**
	 *
	 */
	const DIRECTORY = 'cfg';

	/**
	 * @var null
	 */
	protected $id = null;

	/**
	 * @var null
	 */
	protected $partner_id = null;

	/**
	 * @var array
	 */
	protected $store = array();


	/**
	 * @param      $match
	 * @param      $type
	 * @param null $partner_id
	 * @throws Exception
	 */
	public function load($match, $type, $partner_id = null) {
		$this->partner_id = $partner_id;

		if ($type == self::MATCH_TYPE_DOMAIN) {
			$matches = include(ROOT_PATH . self::DIRECTORY . DS . 'matches.php'); // Da ne se zarezda v property stoto se polzva ednokratno

			foreach ($matches as $row) {
				foreach ($row['match'] as $match_check) {
					if ($match_check == $match) {
						$this->id = $row['id'];
						break;
					}
				}

				if (isset($this->id)) {
					break;
				}
			}
		} elseif ($type == self::MATCH_TYPE_ID) {
			$this->id = $match;
		}

		if (!isset($this->id)) {
			throw new Exception('No cfg match: ' . $match);
		}

		$this->reloadStore();
	}


	/**
	 *
	 */
	public function reloadStore() {
		$this->setStoreData($this->getFile('base.php'));

		$this->setStoreData($this->getFile('store' . DS . $this->id . '.php'));

		if ($this->partner_id) {
			$parner_cfg = 'store' . DS . $this->id . '.' . $this->partner_id . '.php';
			if ($this->isFile($parner_cfg)) {
				$this->setStoreData($this->getFile($parner_cfg));
			}
		}
	}


	/**
	 * @param array $data
	 */
	public function setStoreData(array $data) {
		$this->store = array_merge($this->store, $data);
	}


	/**
	 * @param $file_name
	 * @return mixed
	 */
	public function getFile($file_name) {
		return include(ROOT_PATH . self::DIRECTORY . DS . $file_name);
	}


	/**
	 * @param $file_name
	 * @return bool
	 */
	public function isFile($file_name) {
		return is_file(ROOT_PATH . self::DIRECTORY . DS . $file_name);
	}


	/**
	 * @param $var
	 * @return null
	 */
	public function __get($var) {
		return (array_key_exists($var, $this->store)) ? $this->store[$var] : null;
	}

	/**
	 * @param $var
	 * @return null
	 */
	public function __set($var, $value) {
		$this->store[$var] = $value;
	}

	/**
	 * @return array
	 */
	public function getAll() {
		return $this->store;
	}

	/**
	 * @return null
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return null
	 */
	public function getPortalId() {
		return $this->partner_id;
	}
}
