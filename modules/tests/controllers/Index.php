<?php


/**
 * Class Tests_IndexController
 */
class Tests_IndexController extends Tests_BootstrapHelper {
	/**
	 *
	 */
	public function indexAction() {

		$argv = $this->getRequest()->getServer('argv');
		$test_to_run = isset($argv[3]) ? $argv[3] : Const_Tests::RUN_ALL_TESTS;
		$test_to_run = str_replace('_', '/', $test_to_run);
		foreach (Core_Files::listFiles(cfg()->tests_path) as $test) {
			if (!strstr($test, $test_to_run . '.php') && $test_to_run != Const_Tests::RUN_ALL_TESTS) {
				continue;
			}

			$class_path = str_replace(cfg()->tests_path, '', $test);
			$class_path_frag = explode('/', $class_path);
			$class_path_frag = array_map("ucfirst", $class_path_frag);
			$class_path_frag[count($class_path_frag) - 1] = str_replace('.php', '', $class_path_frag[count($class_path_frag) - 1]);
			$class = implode('_', $class_path_frag);
			$class_name = 'Tests_' . $class . 'File';


			echo '--------------------------------------' . PHP_EOL;
			echo 'Running test: ' . $class_name . PHP_EOL;
			echo '--------------------------------------' . PHP_EOL;

			$phpunit = new PHPUnit_TextUI_TestRunner;
			$phpunit->run($phpunit->getTest(
				$class_name,
				$test
			), array(
				'colors' => 'auto'
			));

			if ($test_to_run != Const_Tests::RUN_ALL_TESTS) {
				die;
			}

			echo PHP_EOL . PHP_EOL;

		}
		die;
	}
}
