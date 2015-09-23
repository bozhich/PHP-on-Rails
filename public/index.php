<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'paths.php';
require_once LIB_PATH . 'Core' . DS . 'Application.php';
new Core_Application();