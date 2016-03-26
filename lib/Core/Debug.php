<?php
use DebugBar\StandardDebugBar;

/**
 * Class Core_Debug
 */
class Core_Debug extends Core_Singleton {
	protected $barInstance;

	/**
	 * @return StandardDebugBar
	 */
	public function getBar() {
		if (!$this->barInstance) {
			$this->barInstance = new StandardDebugBar();
		}

		return $this->barInstance;
	}

	/**
	 * @param PDO $pdo
	 * @throws \DebugBar\DebugBarException
	 * @deprecated in conflict with custom PDO statement class
	 */
	public function initPdoDebug(PDO $pdo) {

		$pdoRead = new DebugBar\DataCollector\PDO\TraceablePDO($pdo);
		$pdoWrite = new DebugBar\DataCollector\PDO\TraceablePDO($pdo);

		$pdoCollector = new DebugBar\DataCollector\PDO\PDOCollector();
		$pdoCollector->addConnection($pdoRead, 'write-db');
		$pdoCollector->addConnection($pdoWrite, 'read-db');

		$this->getBar()->addCollector($pdoCollector);
	}

}
